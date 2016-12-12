<?php

namespace Framework\View\Twig\Node\Expression\Test;

use Framework\View\Twig\Compiler;
use Framework\View\Twig\Error\ErrorSyntax;
use Framework\View\Twig\Node\Expression\NodeExpressionArray;
use Framework\View\Twig\Node\Expression\NodeExpressionBlockReference;
use Framework\View\Twig\Node\Expression\NodeExpressionConstant;
use Framework\View\Twig\Node\Expression\NodeExpressionFunction;
use Framework\View\Twig\Node\Expression\NodeExpressionGetAttr;
use Framework\View\Twig\Node\Expression\NodeExpressionName;
use Framework\View\Twig\Node\Expression\NodeExpressionTest;
use Framework\View\Twig\NodeInterface;

/*
 * This file is part of Twig.
 *
 * (c) 2011 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Checks if a variable is defined in the current context.
 *
 * <pre>
 * {# defined works with variable names and variable attributes #}
 * {% if foo is defined %}
 *     {# ... #}
 * {% endif %}
 * </pre>
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class NodeExpressionTestDefined extends NodeExpressionTest
{
    public function __construct(NodeInterface $node, $name, NodeInterface $arguments = null, $lineno)
    {
        if ($node instanceof NodeExpressionName) {
            $node->setAttribute('is_defined_test', true);
        } elseif ($node instanceof NodeExpressionGetAttr) {
            $node->setAttribute('is_defined_test', true);
            $this->changeIgnoreStrictCheck($node);
        } elseif ($node instanceof NodeExpressionBlockReference) {
            $node->setAttribute('is_defined_test', true);
        } elseif ($node instanceof NodeExpressionFunction && 'constant' === $node->getAttribute('name')) {
            $node->setAttribute('is_defined_test', true);
        } elseif ($node instanceof NodeExpressionConstant || $node instanceof NodeExpressionArray) {
            $node = new NodeExpressionConstant(true, $node->getTemplateLine());
        } else {
            throw new ErrorSyntax('The "defined" test only works with simple variables.', $this->getTemplateLine());
        }

        parent::__construct($node, $name, $arguments, $lineno);
    }

    protected function changeIgnoreStrictCheck(NodeExpressionGetAttr $node)
    {
        $node->setAttribute('ignore_strict_check', true);

        if ($node->getNode('node') instanceof NodeExpressionGetAttr) {
            $this->changeIgnoreStrictCheck($node->getNode('node'));
        }
    }

    public function compile(Compiler $compiler)
    {
        $compiler->subcompile($this->getNode('node'));
    }
}
