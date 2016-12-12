<?php

namespace Framework\View\Twig\NodeVisitor;

use Framework\View\Twig\BaseNodeVisitor;
use Framework\View\Twig\Environment;
use Framework\View\Twig\Node;
use Framework\View\Twig\Node\Expression\NodeExpressionFilter;
use Framework\View\Twig\Node\Expression\NodeExpressionFunction;
use Framework\View\Twig\Node\NodeCheckSecurity;
use Framework\View\Twig\Node\NodeModule;
use Framework\View\Twig\Node\NodePrint;
use Framework\View\Twig\Node\NodeSandboxedPrint;

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * NodeVisitorSandbox implements sandboxing.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class NodeVisitorSandbox extends BaseNodeVisitor
{
    protected $inAModule = false;
    protected $tags;
    protected $filters;
    protected $functions;

    /**
     * {@inheritdoc}
     */
    protected function doEnterNode(Node $node, Environment $env)
    {
        if ($node instanceof NodeModule) {
            $this->inAModule = true;
            $this->tags = array();
            $this->filters = array();
            $this->functions = array();

            return $node;
        } elseif ($this->inAModule) {
            // look for tags
            if ($node->getNodeTag() && !isset($this->tags[$node->getNodeTag()])) {
                $this->tags[$node->getNodeTag()] = $node;
            }

            // look for filters
            if ($node instanceof NodeExpressionFilter && !isset($this->filters[$node->getNode('filter')->getAttribute('value')])) {
                $this->filters[$node->getNode('filter')->getAttribute('value')] = $node;
            }

            // look for functions
            if ($node instanceof NodeExpressionFunction && !isset($this->functions[$node->getAttribute('name')])) {
                $this->functions[$node->getAttribute('name')] = $node;
            }

            // wrap print to check __toString() calls
            if ($node instanceof NodePrint) {
                return new NodeSandboxedPrint($node->getNode('expr'), $node->getTemplateLine(), $node->getNodeTag());
            }
        }

        return $node;
    }

    /**
     * {@inheritdoc}
     */
    protected function doLeaveNode(Node $node, Environment $env)
    {
        if ($node instanceof NodeModule) {
            $this->inAModule = false;

            $node->setNode('display_start', new Node(array(new NodeCheckSecurity($this->filters, $this->tags, $this->functions), $node->getNode('display_start'))));
        }

        return $node;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 0;
    }
}
