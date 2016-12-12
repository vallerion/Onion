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

class NodeExpressionBinaryStartsWith extends NodeExpressionBinary
{
    public function compile(Compiler $compiler)
    {
        $left = $compiler->getVarName();
        $right = $compiler->getVarName();
        $compiler
            ->raw(sprintf('(is_string($%s = ', $left))
            ->subcompile($this->getNode('left'))
            ->raw(sprintf(') && is_string($%s = ', $right))
            ->subcompile($this->getNode('right'))
            ->raw(sprintf(') && (\'\' === $%2$s || 0 === strpos($%1$s, $%2$s)))', $left, $right))
        ;
    }

    public function operator(Compiler $compiler)
    {
        return $compiler->raw('');
    }
}
