<?php

namespace Framework\View\Twig\NodeVisitor;

use Framework\View\Twig\BaseNodeVisitor;
use Framework\View\Twig\Environment;
use Framework\View\Twig\Node;
use Framework\View\Twig\Node\Expression\NodeExpressionConstant;
use Framework\View\Twig\Node\Expression\NodeExpressionFilter;
use Framework\View\Twig\Node\NodeAutoEscape;
use Framework\View\Twig\Node\NodeBlock;
use Framework\View\Twig\Node\NodeBlockReference;
use Framework\View\Twig\Node\NodeImport;
use Framework\View\Twig\Node\NodeModule;
use Framework\View\Twig\Node\NodePrint;
use Framework\View\Twig\NodeInterface;
use Framework\View\Twig\NodeTraverser;

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * NodeVisitor_Escaper implements output escaping.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class NodeVisitorEscaper extends BaseNodeVisitor
{
    protected $statusStack = array();
    protected $blocks = array();
    protected $safeAnalysis;
    protected $traverser;
    protected $defaultStrategy = false;
    protected $safeVars = array();

    public function __construct()
    {
        $this->safeAnalysis = new NodeVisitorSafeAnalysis();
    }

    /**
     * {@inheritdoc}
     */
    protected function doEnterNode(Node $node, Environment $env)
    {
        if ($node instanceof NodeModule) {
            if ($env->hasExtension('ExtensionEscaper') && $defaultStrategy = $env->getExtension('ExtensionEscaper')->getDefaultStrategy($node->getTemplateName())) {
                $this->defaultStrategy = $defaultStrategy;
            }
            $this->safeVars = array();
            $this->blocks = array();
        } elseif ($node instanceof NodeAutoEscape) {
            $this->statusStack[] = $node->getAttribute('value');
        } elseif ($node instanceof NodeBlock) {
            $this->statusStack[] = isset($this->blocks[$node->getAttribute('name')]) ? $this->blocks[$node->getAttribute('name')] : $this->needEscaping($env);
        } elseif ($node instanceof NodeImport) {
            $this->safeVars[] = $node->getNode('var')->getAttribute('name');
        }

        return $node;
    }

    /**
     * {@inheritdoc}
     */
    protected function doLeaveNode(Node $node, Environment $env)
    {
        if ($node instanceof NodeModule) {
            $this->defaultStrategy = false;
            $this->safeVars = array();
            $this->blocks = array();
        } elseif ($node instanceof NodeExpressionFilter) {
            return $this->preEscapeFilterNode($node, $env);
        } elseif ($node instanceof NodePrint) {
            return $this->escapePrintNode($node, $env, $this->needEscaping($env));
        }

        if ($node instanceof NodeAutoEscape || $node instanceof NodeBlock) {
            array_pop($this->statusStack);
        } elseif ($node instanceof NodeBlockReference) {
            $this->blocks[$node->getAttribute('name')] = $this->needEscaping($env);
        }

        return $node;
    }

    protected function escapePrintNode(NodePrint $node, Environment $env, $type)
    {
        if (false === $type) {
            return $node;
        }

        $expression = $node->getNode('expr');

        if ($this->isSafeFor($type, $expression, $env)) {
            return $node;
        }

        $class = get_class($node);

        return new $class(
            $this->getEscaperFilter($type, $expression),
            $node->getTemplateLine()
        );
    }

    protected function preEscapeFilterNode(NodeExpressionFilter $filter, Environment $env)
    {
        $name = $filter->getNode('filter')->getAttribute('value');

        $type = $env->getFilter($name)->getPreEscape();
        if (null === $type) {
            return $filter;
        }

        $node = $filter->getNode('node');
        if ($this->isSafeFor($type, $node, $env)) {
            return $filter;
        }

        $filter->setNode('node', $this->getEscaperFilter($type, $node));

        return $filter;
    }

    protected function isSafeFor($type, NodeInterface $expression, $env)
    {
        $safe = $this->safeAnalysis->getSafe($expression);

        if (null === $safe) {
            if (null === $this->traverser) {
                $this->traverser = new NodeTraverser($env, array($this->safeAnalysis));
            }

            $this->safeAnalysis->setSafeVars($this->safeVars);

            $this->traverser->traverse($expression);
            $safe = $this->safeAnalysis->getSafe($expression);
        }

        return in_array($type, $safe) || in_array('all', $safe);
    }

    protected function needEscaping(Environment $env)
    {
        if (count($this->statusStack)) {
            return $this->statusStack[count($this->statusStack) - 1];
        }

        return $this->defaultStrategy ? $this->defaultStrategy : false;
    }

    protected function getEscaperFilter($type, NodeInterface $node)
    {
        $line = $node->getTemplateLine();
        $name = new NodeExpressionConstant('escape', $line);
        $args = new Node(array(new NodeExpressionConstant((string) $type, $line), new NodeExpressionConstant(null, $line), new NodeExpressionConstant(true, $line)));

        return new NodeExpressionFilter($node, $name, $args, $line);
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 0;
    }
}
