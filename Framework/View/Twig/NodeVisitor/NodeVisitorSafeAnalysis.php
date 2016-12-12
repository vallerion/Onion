<?php

namespace Framework\View\Twig\NodeVisitor;

use Framework\View\Twig\BaseNodeVisitor;
use Framework\View\Twig\Environment;
use Framework\View\Twig\Node;
use Framework\View\Twig\Node\Expression\NodeExpressionBlockReference;
use Framework\View\Twig\Node\Expression\NodeExpressionConditional;
use Framework\View\Twig\Node\Expression\NodeExpressionConstant;
use Framework\View\Twig\Node\Expression\NodeExpressionFilter;
use Framework\View\Twig\Node\Expression\NodeExpressionFunction;
use Framework\View\Twig\Node\Expression\NodeExpressionGetAttr;
use Framework\View\Twig\Node\Expression\NodeExpressionMethodCall;
use Framework\View\Twig\Node\Expression\NodeExpressionName;
use Framework\View\Twig\Node\Expression\NodeExpressionParent;
use Framework\View\Twig\NodeInterface;

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


class NodeVisitorSafeAnalysis extends BaseNodeVisitor
{
    protected $data = array();
    protected $safeVars = array();

    public function setSafeVars($safeVars)
    {
        $this->safeVars = $safeVars;
    }

    public function getSafe(NodeInterface $node)
    {
        $hash = spl_object_hash($node);
        if (!isset($this->data[$hash])) {
            return;
        }

        foreach ($this->data[$hash] as $bucket) {
            if ($bucket['key'] !== $node) {
                continue;
            }

            if (in_array('html_attr', $bucket['value'])) {
                $bucket['value'][] = 'html';
            }

            return $bucket['value'];
        }
    }

    protected function setSafe(NodeInterface $node, array $safe)
    {
        $hash = spl_object_hash($node);
        if (isset($this->data[$hash])) {
            foreach ($this->data[$hash] as &$bucket) {
                if ($bucket['key'] === $node) {
                    $bucket['value'] = $safe;

                    return;
                }
            }
        }
        $this->data[$hash][] = array(
            'key' => $node,
            'value' => $safe,
        );
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
        if ($node instanceof NodeExpressionConstant) {
            // constants are marked safe for all
            $this->setSafe($node, array('all'));
        } elseif ($node instanceof NodeExpressionBlockReference) {
            // blocks are safe by definition
            $this->setSafe($node, array('all'));
        } elseif ($node instanceof NodeExpressionParent) {
            // parent block is safe by definition
            $this->setSafe($node, array('all'));
        } elseif ($node instanceof NodeExpressionConditional) {
            // intersect safeness of both operands
            $safe = $this->intersectSafe($this->getSafe($node->getNode('expr2')), $this->getSafe($node->getNode('expr3')));
            $this->setSafe($node, $safe);
        } elseif ($node instanceof NodeExpressionFilter) {
            // filter expression is safe when the filter is safe
            $name = $node->getNode('filter')->getAttribute('value');
            $args = $node->getNode('arguments');
            if (false !== $filter = $env->getFilter($name)) {
                $safe = $filter->getSafe($args);
                if (null === $safe) {
                    $safe = $this->intersectSafe($this->getSafe($node->getNode('node')), $filter->getPreservesSafety());
                }
                $this->setSafe($node, $safe);
            } else {
                $this->setSafe($node, array());
            }
        } elseif ($node instanceof NodeExpressionFunction) {
            // function expression is safe when the function is safe
            $name = $node->getAttribute('name');
            $args = $node->getNode('arguments');
            $function = $env->getFunction($name);
            if (false !== $function) {
                $this->setSafe($node, $function->getSafe($args));
            } else {
                $this->setSafe($node, array());
            }
        } elseif ($node instanceof NodeExpressionMethodCall) {
            if ($node->getAttribute('safe')) {
                $this->setSafe($node, array('all'));
            } else {
                $this->setSafe($node, array());
            }
        } elseif ($node instanceof NodeExpressionGetAttr && $node->getNode('node') instanceof NodeExpressionName) {
            $name = $node->getNode('node')->getAttribute('name');
            // attributes on template instances are safe
            if ('_self' == $name || in_array($name, $this->safeVars)) {
                $this->setSafe($node, array('all'));
            } else {
                $this->setSafe($node, array());
            }
        } else {
            $this->setSafe($node, array());
        }

        return $node;
    }

    protected function intersectSafe(array $a = null, array $b = null)
    {
        if (null === $a || null === $b) {
            return array();
        }

        if (in_array('all', $a)) {
            return $b;
        }

        if (in_array('all', $b)) {
            return $a;
        }

        return array_intersect($a, $b);
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 0;
    }
}
