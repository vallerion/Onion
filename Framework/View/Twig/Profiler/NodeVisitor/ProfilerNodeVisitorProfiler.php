<?php

namespace Framework\View\Twig\Profiler\NodeVisitor;

use Framework\View\Twig\BaseNodeVisitor;
use Framework\View\Twig\Environment;
use Framework\View\Twig\Node;
use Framework\View\Twig\Node\NodeBlock;
use Framework\View\Twig\Node\NodeBody;
use Framework\View\Twig\Node\NodeMacro;
use Framework\View\Twig\Node\NodeModule;
use Framework\View\Twig\Profiler\Node\ProfilerNodeEnterProfile;
use Framework\View\Twig\Profiler\Node\ProfilerNodeLeaveProfile;
use Framework\View\Twig\Profiler\ProfilerProfile;

/*
 * This file is part of Twig.
 *
 * (c) 2015 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ProfilerNodeVisitorProfiler extends BaseNodeVisitor
{
    private $extensionName;

    public function __construct($extensionName)
    {
        $this->extensionName = $extensionName;
    }

    /**
     * {@inheritdoc}
     */
    protected function doEnterNode(Node $node, Environment $env)
    {
        return $node;
    }

    /**
     * {@inheritdoc}
     */
    protected function doLeaveNode(Node $node, Environment $env)
    {
        if ($node instanceof NodeModule) {
            $varName = $this->getVarName();
            $node->setNode('display_start', new Node(array(new ProfilerNodeEnterProfile($this->extensionName, ProfilerProfile::TEMPLATE, $node->getTemplateName(), $varName), $node->getNode('display_start'))));
            $node->setNode('display_end', new Node(array(new ProfilerNodeLeaveProfile($varName), $node->getNode('display_end'))));
        } elseif ($node instanceof NodeBlock) {
            $varName = $this->getVarName();
            $node->setNode('body', new NodeBody(array(
                new ProfilerNodeEnterProfile($this->extensionName, ProfilerProfile::BLOCK, $node->getAttribute('name'), $varName),
                $node->getNode('body'),
                new ProfilerNodeLeaveProfile($varName),
            )));
        } elseif ($node instanceof NodeMacro) {
            $varName = $this->getVarName();
            $node->setNode('body', new NodeBody(array(
                new ProfilerNodeEnterProfile($this->extensionName, ProfilerProfile::MACRO, $node->getAttribute('name'), $varName),
                $node->getNode('body'),
                new ProfilerNodeLeaveProfile($varName),
            )));
        }

        return $node;
    }

    private function getVarName()
    {
        return sprintf('__internal_%s', hash('sha256', uniqid(mt_rand(), true), false));
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 0;
    }
}
