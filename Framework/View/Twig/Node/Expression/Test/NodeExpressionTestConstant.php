<?php

namespace Framework\View\Twig\Node\Expression\Test;

use Framework\View\Twig\Compiler;
use Framework\View\Twig\Node\Expression\NodeExpressionTest;

/*
 * This file is part of Twig.
 *
 * (c) 2011 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Checks if a variable is the exact same value as a constant.
 *
 * <pre>
 *  {% if post.status is constant('Post::PUBLISHED') %}
 *    the status attribute is exactly the same as Post::PUBLISHED
 *  {% endif %}
 * </pre>
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class NodeExpressionTestConstant extends NodeExpressionTest
{
    public function compile(Compiler $compiler)
    {
        $compiler
            ->raw('(')
            ->subcompile($this->getNode('node'))
            ->raw(' === constant(')
        ;

        if ($this->getNode('arguments')->hasNode(1)) {
            $compiler
                ->raw('get_class(')
                ->subcompile($this->getNode('arguments')->getNode(1))
                ->raw(')."::".')
            ;
        }

        $compiler
            ->subcompile($this->getNode('arguments')->getNode(0))
            ->raw('))')
        ;
    }
}
