<?php

namespace Framework\View\Twig\Node\Expression\Binary;

use Framework\View\Twig\Compiler;
use Framework\View\Twig\Node\Expression\NodeExpressionBinary;

/*
 * This file is part of Twig.
 *
 * (c) 2013 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class NodeExpressionBinaryMatches extends NodeExpressionBinary
{
    public function compile(Compiler $compiler)
    {
        $compiler
            ->raw('preg_match(')
            ->subcompile($this->getNode('right'))
            ->raw(', ')
            ->subcompile($this->getNode('left'))
            ->raw(')')
        ;
    }

    public function operator(Compiler $compiler)
    {
        return $compiler->raw('');
    }
}
