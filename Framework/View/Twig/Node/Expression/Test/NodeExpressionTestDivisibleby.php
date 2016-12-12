<?php

namespace Framework\View\Twig\Node\Expression\Test;

use Framework\View\Twig\Compiler;
use Framework\View\Twig\Node\Expression\NodeExpressionTest;

/*
 * This file is part of Twig.
 *
 * (c) 2011 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Checks if a variable is divisible by a number.
 *
 * <pre>
 *  {% if loop.index is divisible by(3) %}
 * </pre>
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class NodeExpressionTestDivisibleby extends NodeExpressionTest
{
    public function compile(Compiler $compiler)
    {
        $compiler
            ->raw('(0 == ')
            ->subcompile($this->getNode('node'))
            ->raw(' % ')
            ->subcompile($this->getNode('arguments')->getNode(0))
            ->raw(')')
        ;
    }
}
