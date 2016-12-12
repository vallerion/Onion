<?php

namespace Framework\View\Twig\Node\Expression\Unary;

use Framework\View\Twig\Compiler;
use Framework\View\Twig\Node\Expression\NodeExpressionUnary;

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 * (c) 2009 Armin Ronacher
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class NodeExpressionUnaryPos extends NodeExpressionUnary
{
    public function operator(Compiler $compiler)
    {
        $compiler->raw('+');
    }
}
