<?php

namespace Framework\View\Twig\Node\Expression\Test;

/*
 * This file is part of Twig.
 *
 * (c) 2011 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Framework\View\Twig\Compiler;
use Framework\View\Twig\Node\Expression\NodeExpressionTest;

/**
 * Checks if a number is even.
 *
 * <pre>
 *  {{ var is even }}
 * </pre>
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class NodeExpressionTestEven extends NodeExpressionTest
{
    public function compile(Compiler $compiler)
    {
        $compiler
            ->raw('(')
            ->subcompile($this->getNode('node'))
            ->raw(' % 2 == 0')
            ->raw(')')
        ;
    }
}
