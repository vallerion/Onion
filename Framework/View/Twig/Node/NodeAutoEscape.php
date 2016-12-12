<?php

namespace Framework\View\Twig\Node;

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Framework\View\Twig\Compiler;
use Framework\View\Twig\Node;
use Framework\View\Twig\NodeInterface;

/**
 * Represents an autoescape node.
 *
 * The value is the escaping strategy (can be html, js, ...)
 *
 * The true value is equivalent to html.
 *
 * If autoescaping is disabled, then the value is false.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class NodeAutoEscape extends Node
{
    public function __construct($value, NodeInterface $body, $lineno, $tag = 'autoescape')
    {
        parent::__construct(array('body' => $body), array('value' => $value), $lineno, $tag);
    }

    public function compile(Compiler $compiler)
    {
        $compiler->subcompile($this->getNode('body'));
    }
}
