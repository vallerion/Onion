<?php

namespace Framework\View\Twig\TokenParser;

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
use Framework\View\Twig\Node\Expression\NodeExpressionAssignName;
use Framework\View\Twig\Node\Expression\NodeExpressionConstant;
use Framework\View\Twig\Node\Expression\NodeExpressionGetAttr;
use Framework\View\Twig\Node\Expression\NodeExpressionName;
use Framework\View\Twig\Node\NodeFor;
use Framework\View\Twig\NodeInterface;
use Framework\View\Twig\Token;
use Framework\View\Twig\TokenParser;
use Framework\View\Twig\TokenStream;

/**
 * Loops over each item of a sequence.
 *
 * <pre>
 * <ul>
 *  {% for user in users %}
 *    <li>{{ user.username|e }}</li>
 *  {% endfor %}
 * </ul>
 * </pre>
 */
class TokenParserFor extends TokenParser
{
    public function parse(Token $token)
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();
        $targets = $this->parser->getExpressionParser()->parseAssignmentExpression();
        $stream->expect(Token::OPERATOR_TYPE, 'in');
        $seq = $this->parser->getExpressionParser()->parseExpression();

        $ifexpr = null;
        if ($stream->nextIf(Token::NAME_TYPE, 'if')) {
            $ifexpr = $this->parser->getExpressionParser()->parseExpression();
        }

        $stream->expect(Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse(array($this, 'decideForFork'));
        if ($stream->next()->getValue() == 'else') {
            $stream->expect(Token::BLOCK_END_TYPE);
            $else = $this->parser->subparse(array($this, 'decideForEnd'), true);
        } else {
            $else = null;
        }
        $stream->expect(Token::BLOCK_END_TYPE);

        if (count($targets) > 1) {
            $keyTarget = $targets->getNode(0);
            $keyTarget = new NodeExpressionAssignName($keyTarget->getAttribute('name'), $keyTarget->getTemplateLine());
            $valueTarget = $targets->getNode(1);
            $valueTarget = new NodeExpressionAssignName($valueTarget->getAttribute('name'), $valueTarget->getTemplateLine());
        } else {
            $keyTarget = new NodeExpressionAssignName('_key', $lineno);
            $valueTarget = $targets->getNode(0);
            $valueTarget = new NodeExpressionAssignName($valueTarget->getAttribute('name'), $valueTarget->getTemplateLine());
        }

        if ($ifexpr) {
            $this->checkLoopUsageCondition($stream, $ifexpr);
            $this->checkLoopUsageBody($stream, $body);
        }

        return new NodeFor($keyTarget, $valueTarget, $seq, $ifexpr, $body, $else, $lineno, $this->getTag());
    }

    public function decideForFork(Token $token)
    {
        return $token->test(array('else', 'endfor'));
    }

    public function decideForEnd(Token $token)
    {
        return $token->test('endfor');
    }

    // the loop variable cannot be used in the condition
    protected function checkLoopUsageCondition(TokenStream $stream, NodeInterface $node)
    {
        if ($node instanceof NodeExpressionGetAttr && $node->getNode('node') instanceof NodeExpressionName && 'loop' == $node->getNode('node')->getAttribute('name')) {
            throw new ErrorSyntax('The "loop" variable cannot be used in a looping condition.', $node->getTemplateLine(), $stream->getSourceContext()->getName());
        }

        foreach ($node as $n) {
            if (!$n) {
                continue;
            }

            $this->checkLoopUsageCondition($stream, $n);
        }
    }

    // check usage of non-defined loop-items
    // it does not catch all problems (for instance when a for is included into another or when the variable is used in an include)
    protected function checkLoopUsageBody(TokenStream $stream, NodeInterface $node)
    {
        if ($node instanceof NodeExpressionGetAttr && $node->getNode('node') instanceof NodeExpressionName && 'loop' == $node->getNode('node')->getAttribute('name')) {
            $attribute = $node->getNode('attribute');
            if ($attribute instanceof NodeExpressionConstant && in_array($attribute->getAttribute('value'), array('length', 'revindex0', 'revindex', 'last'))) {
                throw new ErrorSyntax(sprintf('The "loop.%s" variable is not defined when looping with a condition.', $attribute->getAttribute('value')), $node->getTemplateLine(), $stream->getSourceContext()->getName());
            }
        }

        // should check for parent.loop.XXX usage
        if ($node instanceof NodeFor) {
            return;
        }

        foreach ($node as $n) {
            if (!$n) {
                continue;
            }

            $this->checkLoopUsageBody($stream, $n);
        }
    }

    public function getTag()
    {
        return 'for';
    }
}
