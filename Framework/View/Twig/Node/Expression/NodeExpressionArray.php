<?php

namespace Framework\View\Twig\Node\Expression;

use Framework\View\Twig\Node\NodeExpression;
use Framework\View\Twig\Compiler;

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class NodeExpressionArray extends NodeExpression
{
    protected $index;

    public function __construct(array $elements, $lineno)
    {
        parent::__construct($elements, array(), $lineno);

        $this->index = -1;
        foreach ($this->getKeyValuePairs() as $pair) {
            if ($pair['key'] instanceof NodeExpressionConstant && ctype_digit((string) $pair['key']->getAttribute('value')) && $pair['key']->getAttribute('value') > $this->index) {
                $this->index = $pair['key']->getAttribute('value');
            }
        }
    }

    public function getKeyValuePairs()
    {
        $pairs = array();

        foreach (array_chunk($this->nodes, 2) as $pair) {
            $pairs[] = array(
                'key' => $pair[0],
                'value' => $pair[1],
            );
        }

        return $pairs;
    }

    public function hasElement(NodeExpression $key)
    {
        foreach ($this->getKeyValuePairs() as $pair) {
            // we compare the string representation of the keys
            // to avoid comparing the line numbers which are not relevant here.
            if ((string) $key === (string) $pair['key']) {
                return true;
            }
        }

        return false;
    }

    public function addElement(NodeExpression $value, NodeExpression $key = null)
    {
        if (null === $key) {
            $key = new NodeExpressionConstant(++$this->index, $value->getTemplateLine());
        }

        array_push($this->nodes, $key, $value);
    }

    public function compile(Compiler $compiler)
    {
        $compiler->raw('array(');
        $first = true;
        foreach ($this->getKeyValuePairs() as $pair) {
            if (!$first) {
                $compiler->raw(', ');
            }
            $first = false;

            $compiler
                ->subcompile($pair['key'])
                ->raw(' => ')
                ->subcompile($pair['value'])
            ;
        }
        $compiler->raw(')');
    }
}
