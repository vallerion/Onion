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
 * Represents a profile leave node.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ProfilerNodeLeaveProfile extends Node
{
    public function __construct($varName)
    {
        parent::__construct(array(), array('var_name' => $varName));
    }

    /**
     * {@inheritdoc}
     */
    public function compile(Compiler $compiler)
    {
        $compiler
            ->write("\n")
            ->write(sprintf("\$%s->leave(\$%s);\n\n", $this->getAttribute('var_name'), $this->getAttribute('var_name').'_prof'))
        ;
    }
}
