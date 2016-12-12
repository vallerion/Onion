<?php

namespace Framework\View\Twig;

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 * (c) 2009 Armin Ronacher
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Framework\View\Twig\Error\ErrorSyntax;
use Framework\View\Twig\Node\NodeBlock;
use Framework\View\Twig\Node\NodeBlockReference;
use Framework\View\Twig\Node\NodeBody;
use Framework\View\Twig\Node\NodeExpression;
use Framework\View\Twig\Node\NodeMacro;
use Framework\View\Twig\Node\NodeModule;
use Framework\View\Twig\Node\NodePrint;
use Framework\View\Twig\Node\NodeSet;
use Framework\View\Twig\Node\NodeText;
use ReflectionClass;

/**
 * Default parser implementation.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Parser implements ParserInterface
{
    protected $stack = array();
    protected $stream;
    protected $parent;
    protected $handlers;
    protected $visitors;
    protected $expressionParser;
    protected $blocks;
    protected $blockStack;
    protected $macros;
    protected $env;
    protected $reservedMacroNames;
    protected $importedSymbols;
    protected $traits;
    protected $embeddedTemplates = array();

    public function __construct(Environment $env)
    {
        $this->env = $env;
    }

    /**
     * @deprecated since 1.27 (to be removed in 2.0)
     */
    public function getEnvironment()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0.', E_USER_DEPRECATED);

        return $this->env;
    }

    public function getVarName()
    {
        return sprintf('__internal_%s', hash('sha256', uniqid(mt_rand(), true), false));
    }

    /**
     * @deprecated since 1.27 (to be removed in 2.0). Use $parser->getStream()->getSourceContext()->getPath() instead.
     */
    public function getFilename()
    {
        @trigger_error(sprintf('The "%s" method is deprecated since version 1.27 and will be removed in 2.0. Use $parser->getStream()->getSourceContext()->getPath() instead.', __METHOD__), E_USER_DEPRECATED);

        return $this->stream->getSourceContext()->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function parse(TokenStream $stream, $test = null, $dropNeedle = false)
    {
        // push all variables into the stack to keep the current state of the parser
        // using get_object_vars() instead of foreach would lead to https://bugs.php.net/71336
        // This hack can be removed when min version if PHP 7.0
        $vars = array();
        foreach ($this as $k => $v) {
            $vars[$k] = $v;
        }

        unset($vars['stack'], $vars['env'], $vars['handlers'], $vars['visitors'], $vars['expressionParser'], $vars['reservedMacroNames']);
        $this->stack[] = $vars;

        // tag handlers
        if (null === $this->handlers) {
            $this->handlers = $this->env->getTokenParsers();
            $this->handlers->setParser($this);
        }

        // node visitors
        if (null === $this->visitors) {
            $this->visitors = $this->env->getNodeVisitors();
        }

        if (null === $this->expressionParser) {
            $this->expressionParser = new ExpressionParser($this, $this->env);
        }

        $this->stream = $stream;
        $this->parent = null;
        $this->blocks = array();
        $this->macros = array();
        $this->traits = array();
        $this->blockStack = array();
        $this->importedSymbols = array(array());
        $this->embeddedTemplates = array();

        try {
            $body = $this->subparse($test, $dropNeedle);

            if (null !== $this->parent && null === $body = $this->filterBodyNodes($body)) {
                $body = new Node();
            }
        } catch (ErrorSyntax $e) {
            if (!$e->getTemplateName()) {
                $e->setTemplateName($this->stream->getSourceContext()->getName());
            }

            if (!$e->getTemplateLine()) {
                $e->setTemplateLine($this->stream->getCurrent()->getLine());
            }

            throw $e;
        }

        $node = new NodeModule(new NodeBody(array($body)), $this->parent, new Node($this->blocks), new Node($this->macros), new Node($this->traits), $this->embeddedTemplates, $stream->getSourceContext());

        $traverser = new NodeTraverser($this->env, $this->visitors);

        $node = $traverser->traverse($node);

        // restore previous stack so previous parse() call can resume working
        foreach (array_pop($this->stack) as $key => $val) {
            $this->$key = $val;
        }

        return $node;
    }

    public function subparse($test, $dropNeedle = false)
    {
        $lineno = $this->getCurrentToken()->getLine();
        $rv = array();
        while (!$this->stream->isEOF()) {
            switch ($this->getCurrentToken()->getType()) {
                case Token::TEXT_TYPE:
                    $token = $this->stream->next();
                    $rv[] = new NodeText($token->getValue(), $token->getLine());
                    break;

                case Token::VAR_START_TYPE:
                    $token = $this->stream->next();
                    $expr = $this->expressionParser->parseExpression();
                    $this->stream->expect(Token::VAR_END_TYPE);
                    $rv[] = new NodePrint($expr, $token->getLine());
                    break;

                case Token::BLOCK_START_TYPE:
                    $this->stream->next();
                    $token = $this->getCurrentToken();

                    if ($token->getType() !== Token::NAME_TYPE) {
                        throw new ErrorSyntax('A block must start with a tag name.', $token->getLine(), $this->stream->getSourceContext()->getName());
                    }

                    if (null !== $test && call_user_func($test, $token)) {
                        if ($dropNeedle) {
                            $this->stream->next();
                        }

                        if (1 === count($rv)) {
                            return $rv[0];
                        }

                        return new Node($rv, array(), $lineno);
                    }

                    $subparser = $this->handlers->getTokenParser($token->getValue());
                    if (null === $subparser) {
                        if (null !== $test) {
                            $e = new ErrorSyntax(sprintf('Unexpected "%s" tag', $token->getValue()), $token->getLine(), $this->stream->getSourceContext()->getName());

                            if (is_array($test) && isset($test[0]) && $test[0] instanceof TokenParserInterface) {
                                $e->appendMessage(sprintf(' (expecting closing tag for the "%s" tag defined near line %s).', $test[0]->getTag(), $lineno));
                            }
                        } else {
                            $e = new ErrorSyntax(sprintf('Unknown "%s" tag.', $token->getValue()), $token->getLine(), $this->stream->getSourceContext()->getName());
                            $e->addSuggestions($token->getValue(), array_keys($this->env->getTags()));
                        }

                        throw $e;
                    }

                    $this->stream->next();

                    $node = $subparser->parse($token);
                    if (null !== $node) {
                        $rv[] = $node;
                    }
                    break;

                default:
                    throw new ErrorSyntax('Lexer or parser ended up in unsupported state.', 0, $this->stream->getSourceContext()->getName());
            }
        }

        if (1 === count($rv)) {
            return $rv[0];
        }

        return new Node($rv, array(), $lineno);
    }

    /**
     * @deprecated since 1.27 (to be removed in 2.0)
     */
    public function addHandler($name, $class)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0.', E_USER_DEPRECATED);

        $this->handlers[$name] = $class;
    }

    /**
     * @deprecated since 1.27 (to be removed in 2.0)
     */
    public function addNodeVisitor(NodeVisitorInterface $visitor)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0.', E_USER_DEPRECATED);

        $this->visitors[] = $visitor;
    }

    public function getBlockStack()
    {
        return $this->blockStack;
    }

    public function peekBlockStack()
    {
        return $this->blockStack[count($this->blockStack) - 1];
    }

    public function popBlockStack()
    {
        array_pop($this->blockStack);
    }

    public function pushBlockStack($name)
    {
        $this->blockStack[] = $name;
    }

    public function hasBlock($name)
    {
        return isset($this->blocks[$name]);
    }

    public function getBlock($name)
    {
        return $this->blocks[$name];
    }

    public function setBlock($name, NodeBlock $value)
    {
        $this->blocks[$name] = new NodeBody(array($value), array(), $value->getTemplateLine());
    }

    public function hasMacro($name)
    {
        return isset($this->macros[$name]);
    }

    public function setMacro($name, NodeMacro $node)
    {
        if ($this->isReservedMacroName($name)) {
            throw new ErrorSyntax(sprintf('"%s" cannot be used as a macro name as it is a reserved keyword.', $name), $node->getTemplateLine(), $this->stream->getSourceContext()->getName());
        }

        $this->macros[$name] = $node;
    }

    public function isReservedMacroName($name)
    {
        if (null === $this->reservedMacroNames) {
            $this->reservedMacroNames = array();
            $r = new ReflectionClass($this->env->getBaseTemplateClass());
            foreach ($r->getMethods() as $method) {
                $methodName = strtolower($method->getName());

                if ('get' === substr($methodName, 0, 3) && isset($methodName[3])) {
                    $this->reservedMacroNames[] = substr($methodName, 3);
                }
            }
        }

        return in_array(strtolower($name), $this->reservedMacroNames);
    }

    public function addTrait($trait)
    {
        $this->traits[] = $trait;
    }

    public function hasTraits()
    {
        return count($this->traits) > 0;
    }

    public function embedTemplate(NodeModule $template)
    {
        $template->setIndex(mt_rand());

        $this->embeddedTemplates[] = $template;
    }

    public function addImportedSymbol($type, $alias, $name = null, NodeExpression $node = null)
    {
        $this->importedSymbols[0][$type][$alias] = array('name' => $name, 'node' => $node);
    }

    public function getImportedSymbol($type, $alias)
    {
        foreach ($this->importedSymbols as $functions) {
            if (isset($functions[$type][$alias])) {
                return $functions[$type][$alias];
            }
        }
    }

    public function isMainScope()
    {
        return 1 === count($this->importedSymbols);
    }

    public function pushLocalScope()
    {
        array_unshift($this->importedSymbols, array());
    }

    public function popLocalScope()
    {
        array_shift($this->importedSymbols);
    }

    /**
     * @return ExpressionParser
     */
    public function getExpressionParser()
    {
        return $this->expressionParser;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return TokenStream
     */
    public function getStream()
    {
        return $this->stream;
    }

    /**
     * @return Token
     */
    public function getCurrentToken()
    {
        return $this->stream->getCurrent();
    }

    protected function filterBodyNodes(NodeInterface $node)
    {
        // check that the body does not contain non-empty output nodes
        if (
            ($node instanceof NodeText && !ctype_space($node->getAttribute('data')))
            ||
            (!$node instanceof NodeText && !$node instanceof NodeBlockReference && $node instanceof NodeOutputInterface)
        ) {
            if (false !== strpos((string) $node, chr(0xEF).chr(0xBB).chr(0xBF))) {
                throw new ErrorSyntax('A template that extends another one cannot start with a byte order mark (BOM); it must be removed.', $node->getTemplateLine(), $this->stream->getSourceContext()->getName());
            }

            throw new ErrorSyntax('A template that extends another one cannot include contents outside Twig blocks. Did you forget to put the contents inside a {% block %} tag?', $node->getTemplateLine(), $this->stream->getSourceContext()->getName());
        }

        // bypass "set" nodes as they "capture" the output
        if ($node instanceof NodeSet) {
            return $node;
        }

        if ($node instanceof NodeOutputInterface) {
            return;
        }

        foreach ($node as $k => $n) {
            if (null !== $n && null === $this->filterBodyNodes($n)) {
                $node->removeNode($k);
            }
        }

        return $node;
    }
}
