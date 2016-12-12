<?php

namespace Framework\View\Twig\Node\Expression\Binary;

use Framework\View\Twig\Compiler;
use Framework\View\Twig\Node\Expression\NodeExpressionBinary;

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 * (c) 2009 Armin Ronacher
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class NodeExpressionBinaryBitwiseAnd extends NodeExpressionBinary
{
    public function operator(Compiler $compiler)
    {
        return $compiler->raw('&');
    }
}
