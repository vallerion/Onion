<?php

namespace Framework\View\Twig\Node\Expression;

use Framework\View\Twig\Node\NodeExpression;
use Framework\View\Twig\Compiler;


/*
 * This file is part of Twig.
 *
 * (c) 2012 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class NodeExpressionMethodCall extends NodeExpression
{
    public function __construct(NodeExpression $node, $method, NodeExpressionArray $arguments, $lineno)
    {
        parent::__construct(array('node' => $node, 'arguments' => $arguments), array('method' => $method, 'safe' => false), $lineno);

        if ($node instanceof NodeExpressionName) {
            $node->setAttribute('always_defined', true);
        }
    }

    public function compile(Compiler $compiler)
    {
        $compiler
            ->subcompile($this->getNode('node'))
            ->raw('->')
            ->raw($this->getAttribute('method'))
            ->raw('(')
        ;
        $first = true;
        foreach ($this->getNode('arguments')->getKeyValuePairs() as $pair) {
            if (!$first) {
                $compiler->raw(', ');
            }
            $first = false;

            $compiler->subcompile($pair['value']);
        }
        $compiler->raw(')');
    }
}
