<?php

namespace Framework\View\Twig\Node\Expression;

use Framework\View\Twig\Compiler;
use Framework\View\Twig\Node;
use Framework\View\Twig\Node\Expression\Binary\NodeExpressionBinaryAnd;
use Framework\View\Twig\Node\Expression\Test\NodeExpressionTestDefined;
use Framework\View\Twig\Node\Expression\Test\NodeExpressionTestNull;
use Framework\View\Twig\Node\Expression\Unary\NodeExpressionUnaryNot;
use Framework\View\Twig\NodeInterface;

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class NodeExpressionNullCoalesce extends NodeExpressionConditional
{
    public function __construct(NodeInterface $left, NodeInterface $right, $lineno)
    {
        $test = new NodeExpressionBinaryAnd(
            new NodeExpressionTestDefined(clone $left, 'defined', new Node(), $left->getTemplateLine()),
            new NodeExpressionUnaryNot(new NodeExpressionTestNull($left, 'null', new Node(), $left->getTemplateLine()), $left->getTemplateLine()),
            $left->getTemplateLine()
        );

        parent::__construct($test, $left, $right, $lineno);
    }

    public function compile(Compiler $compiler)
    {
        /*
         * This optimizes only one case. PHP 7 also supports more complex expressions
         * that can return null. So, for instance, if log is defined, log("foo") ?? "..." works,
         * but log($a["foo"]) ?? "..." does not if $a["foo"] is not defined. More advanced
         * cases might be implemented as an optimizer node visitor, but has not been done
         * as benefits are probably not worth the added complexity.
         */
        if (PHP_VERSION_ID >= 70000 && $this->getNode('expr2') instanceof NodeExpressionName) {
            $this->getNode('expr2')->setAttribute('always_defined', true);
            $compiler
                ->raw('((')
                ->subcompile($this->getNode('expr2'))
                ->raw(') ?? (')
                ->subcompile($this->getNode('expr3'))
                ->raw('))')
            ;
        } else {
            parent::compile($compiler);
        }
    }
}
