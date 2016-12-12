<?php

namespace Framework\View\Twig\Node;

use Framework\View\Twig\Compiler;
use Framework\View\Twig\Node;

/*
 * This file is part of Twig.
 *
 * (c) 2011 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Represents a do node.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class NodeDo extends Node
{
    public function __construct(NodeExpression $expr, $lineno, $tag = null)
    {
        parent::__construct(array('expr' => $expr), array(), $lineno, $tag);
    }

    public function compile(Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write('')
            ->subcompile($this->getNode('expr'))
            ->raw(";\n")
        ;
    }
}
