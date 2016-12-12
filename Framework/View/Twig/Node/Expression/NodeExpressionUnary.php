<?php

namespace Framework\View\Twig\Node\Expression;

use Framework\View\Twig\Node\NodeExpression;
use Framework\View\Twig\NodeInterface;
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
abstract class NodeExpressionUnary extends NodeExpression
{
    public function __construct(NodeInterface $node, $lineno)
    {
        parent::__construct(array('node' => $node), array(), $lineno);
    }

    public function compile(Compiler $compiler)
    {
        $compiler->raw(' ');
        $this->operator($compiler);
        $compiler->subcompile($this->getNode('node'));
    }

    abstract public function operator(Compiler $compiler);
}
