<?php

namespace Framework\View\Twig\Node\Expression;

use Framework\View\Twig\Compiler;
use Framework\View\Twig\FilterCallableInterface;
use Framework\View\Twig\NodeInterface;
use Framework\View\Twig\SimpleFilter;

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 * (c) 2009 Armin Ronacher
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


class NodeExpressionFilter extends NodeExpressionCall
{
    public function __construct(NodeInterface $node, NodeExpressionConstant $filterName, NodeInterface $arguments, $lineno, $tag = null)
    {
        parent::__construct(array('node' => $node, 'filter' => $filterName, 'arguments' => $arguments), array(), $lineno, $tag);
    }

    public function compile(Compiler $compiler)
    {
        $name = $this->getNode('filter')->getAttribute('value');
        $filter = $compiler->getEnvironment()->getFilter($name);

        $this->setAttribute('name', $name);
        $this->setAttribute('type', 'filter');
        $this->setAttribute('thing', $filter);
        $this->setAttribute('needs_environment', $filter->needsEnvironment());
        $this->setAttribute('needs_context', $filter->needsContext());
        $this->setAttribute('arguments', $filter->getArguments());
        if ($filter instanceof FilterCallableInterface || $filter instanceof SimpleFilter) {
            $this->setAttribute('callable', $filter->getCallable());
        }
        if ($filter instanceof SimpleFilter) {
            $this->setAttribute('is_variadic', $filter->isVariadic());
        }

        $this->compileCallable($compiler);
    }
}
