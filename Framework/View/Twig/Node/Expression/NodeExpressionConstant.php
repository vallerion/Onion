<?php

namespace Framework\View\Twig\Node\Expression;

use Framework\View\Twig\Node\NodeExpression;
use Framework\View\Twig\Compiler;

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 * (c) 2009 Armin Ronacher
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class NodeExpressionConstant extends NodeExpression
{
    public function __construct($value, $lineno)
    {
        parent::__construct(array(), array('value' => $value), $lineno);
    }

    public function compile(Compiler $compiler)
    {
        $compiler->repr($this->getAttribute('value'));
    }
}
