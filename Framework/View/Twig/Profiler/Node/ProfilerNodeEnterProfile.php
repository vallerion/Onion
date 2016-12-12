<?php

namespace Framework\View\Twig\Profiler\Node;

use Framework\View\Twig\Compiler;
use Framework\View\Twig\Node;

/*
 * This file is part of Twig.
 *
 * (c) 2015 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Represents a profile enter node.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ProfilerNodeEnterProfile extends Node
{
    public function __construct($extensionName, $type, $name, $varName)
    {
        parent::__construct(array(), array('extension_name' => $extensionName, 'name' => $name, 'type' => $type, 'var_name' => $varName));
    }

    /**
     * {@inheritdoc}
     */
    public function compile(Compiler $compiler)
    {
        $compiler
            ->write(sprintf('$%s = $this->env->getExtension(', $this->getAttribute('var_name')))
            ->repr($this->getAttribute('extension_name'))
            ->raw(");\n")
            ->write(sprintf('$%s->enter($%s = new ProfilerProfile($this->getTemplateName(), ', $this->getAttribute('var_name'), $this->getAttribute('var_name').'_prof'))
            ->repr($this->getAttribute('type'))
            ->raw(', ')
            ->repr($this->getAttribute('name'))
            ->raw("));\n\n")
        ;
    }
}
