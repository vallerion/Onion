<?php

namespace Framework\View\Twig\Node\Expression;

use Framework\View\Twig\Compiler;
use Framework\View\Twig\NodeInterface;
use Framework\View\Twig\SimpleTest;
use Framework\View\Twig\TestCallableInterface;

/*
 * This file is part of Twig.
 *
 * (c) 2010 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class NodeExpressionTest extends NodeExpressionCall
{
    public function __construct(NodeInterface $node, $name, NodeInterface $arguments = null, $lineno)
    {
        $nodes = array('node' => $node);
        if (null !== $arguments) {
            $nodes['arguments'] = $arguments;
        }

        parent::__construct($nodes, array('name' => $name), $lineno);
    }

    public function compile(Compiler $compiler)
    {
        $name = $this->getAttribute('name');
        $test = $compiler->getEnvironment()->getTest($name);

        $this->setAttribute('name', $name);
        $this->setAttribute('type', 'test');
        $this->setAttribute('thing', $test);
        if ($test instanceof TestCallableInterface || $test instanceof SimpleTest) {
            $this->setAttribute('callable', $test->getCallable());
        }
        if ($test instanceof SimpleTest) {
            $this->setAttribute('is_variadic', $test->isVariadic());
        }

        $this->compileCallable($compiler);
    }
}
