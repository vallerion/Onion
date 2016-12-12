<?php

namespace Framework\View\Twig\Node;

use Framework\View\Twig\Compiler;
use Framework\View\Twig\Node;
use Framework\View\Twig\NodeInterface;

/*
 * This file is part of Twig.
 *
 * (c) 2010 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Represents a spaceless node.
 *
 * It removes spaces between HTML tags.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class NodeSpaceless extends Node
{
    public function __construct(NodeInterface $body, $lineno, $tag = 'spaceless')
    {
        parent::__construct(array('body' => $body), array(), $lineno, $tag);
    }

    public function compile(Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write("ob_start();\n")
            ->subcompile($this->getNode('body'))
            ->write("echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));\n")
        ;
    }
}
