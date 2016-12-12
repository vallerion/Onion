<?php

namespace Framework\View\Twig\Node;

use Framework\View\Twig\Node;
use Framework\View\Twig\Compiler;
use Framework\View\Twig\NodeOutputInterface;

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 * (c) 2009 Armin Ronacher
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Represents a text node.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class NodeText extends Node implements NodeOutputInterface
{
    public function __construct($data, $lineno)
    {
        parent::__construct(array(), array('data' => $data), $lineno);
    }

    public function compile(Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write('echo ')
            ->string($this->getAttribute('data'))
            ->raw(";\n")
        ;
    }
}
