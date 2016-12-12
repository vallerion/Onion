<?php

namespace Framework\View\Twig\Node\Expression\Binary;

use Framework\View\Twig\Compiler;
use Framework\View\Twig\Node\Expression\NodeExpressionBinary;

/*
 * This file is part of Twig.
 *
 * (c) 2010 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class NodeExpressionBinaryIn extends NodeExpressionBinary
{
    public function compile(Compiler $compiler)
    {
        $compiler
            ->raw('twig_in_filter(')
            ->subcompile($this->getNode('left'))
            ->raw(', ')
            ->subcompile($this->getNode('right'))
            ->raw(')')
        ;
    }

    public function operator(Compiler $compiler)
    {
        return $compiler->raw('in');
    }
}
