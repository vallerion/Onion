<?php

namespace Framework\View\Twig\Node;

/*
 * This file is part of Twig.
 *
 * (c) 2012 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Framework\View\Twig\Compiler;
use Framework\View\Twig\Node\Expression\NodeExpressionConstant;

/**
 * Represents an embed node.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class NodeEmbed extends NodeInclude
{
    // we don't inject the module to avoid node visitors to traverse it twice (as it will be already visited in the main module)
    public function __construct($name, $index, NodeExpression $variables = null, $only = false, $ignoreMissing = false, $lineno, $tag = null)
    {
        parent::__construct(new NodeExpressionConstant('not_used', $lineno), $variables, $only, $ignoreMissing, $lineno, $tag);

        $this->setAttribute('name', $name);
        // to be removed in 2.0, used name instead
        $this->setAttribute('filename', $name);
        $this->setAttribute('index', $index);
    }

    protected function addGetTemplate(Compiler $compiler)
    {
        $compiler
            ->write('$this->loadTemplate(')
            ->string($this->getAttribute('name'))
            ->raw(', ')
            ->repr($this->getTemplateName())
            ->raw(', ')
            ->repr($this->getTemplateLine())
            ->raw(', ')
            ->string($this->getAttribute('index'))
            ->raw(')')
        ;
    }
}
