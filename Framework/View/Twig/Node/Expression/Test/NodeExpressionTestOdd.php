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
 * Checks if a number is odd.
 *
 * <pre>
 *  {{ var is odd }}
 * </pre>
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class NodeExpressionTestOdd extends NodeExpressionTest
{
    public function compile(Compiler $compiler)
    {
        $compiler
            ->raw('(')
            ->subcompile($this->getNode('node'))
            ->raw(' % 2 == 1')
            ->raw(')')
        ;
    }
}
