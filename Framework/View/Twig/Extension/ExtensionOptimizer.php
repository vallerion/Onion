<?php

namespace Framework\View\Twig\Extension;

use Framework\View\Twig\Extension;
use Framework\View\Twig\NodeVisitor\NodeVisitorOptimizer;

/*
 * This file is part of Twig.
 *
 * (c) 2010 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class ExtensionOptimizer extends Extension
{
    protected $optimizers;

    public function __construct($optimizers = -1)
    {
        $this->optimizers = $optimizers;
    }

    public function getNodeVisitors()
    {
        return array(new NodeVisitorOptimizer($this->optimizers));
    }

    public function getName()
    {
        return 'optimizer';
    }
}
