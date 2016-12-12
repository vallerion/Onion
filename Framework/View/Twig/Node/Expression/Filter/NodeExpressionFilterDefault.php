<?php

namespace Framework\View\Twig\Node\Expression\Filter;

use Framework\View\Twig\Compiler;
use Framework\View\Twig\Node;
use Framework\View\Twig\Node\Expression\NodeExpressionConditional;
use Framework\View\Twig\Node\Expression\NodeExpressionConstant;
use Framework\View\Twig\Node\Expression\NodeExpressionFilter;
use Framework\View\Twig\Node\Expression\NodeExpressionGetAttr;
use Framework\View\Twig\Node\Expression\NodeExpressionName;
use Framework\View\Twig\Node\Expression\Test\NodeExpressionTestDefined;
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
 * Returns the value or the default value when it is undefined or empty.
 *
 * <pre>
 *  {{ var.foo|default('foo item on var is not defined') }}
 * </pre>
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class NodeExpressionFilterDefault extends NodeExpressionFilter
{
    public function __construct(NodeInterface $node, NodeExpressionConstant $filterName, NodeInterface $arguments, $lineno, $tag = null)
    {
        $default = new NodeExpressionFilter($node, new NodeExpressionConstant('default', $node->getTemplateLine()), $arguments, $node->getTemplateLine());

        if ('default' === $filterName->getAttribute('value') && ($node instanceof NodeExpressionName || $node instanceof NodeExpressionGetAttr)) {
            $test = new NodeExpressionTestDefined(clone $node, 'defined', new Node(), $node->getTemplateLine());
            $false = count($arguments) ? $arguments->getNode(0) : new NodeExpressionConstant('', $node->getTemplateLine());

            $node = new NodeExpressionConditional($test, $default, $false, $node->getTemplateLine());
        } else {
            $node = $default;
        }

        parent::__construct($node, $filterName, $arguments, $lineno, $tag);
    }

    public function compile(Compiler $compiler)
    {
        $compiler->subcompile($this->getNode('node'));
    }
}
