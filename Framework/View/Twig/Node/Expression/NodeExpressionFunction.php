<?php

namespace Framework\View\Twig\Node\Expression;

use Framework\View\Twig\Compiler;
use Framework\View\Twig\FunctionCallableInterface;
use Framework\View\Twig\NodeInterface;
use Framework\View\Twig\SimpleFunction;

/*
 * This file is part of Twig.
 *
 * (c) 2010 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class NodeExpressionFunction extends NodeExpressionCall
{
    public function __construct($name, NodeInterface $arguments, $lineno)
    {
        parent::__construct(array('arguments' => $arguments), array('name' => $name, 'is_defined_test' => false), $lineno);
    }

    public function compile(Compiler $compiler)
    {
        $name = $this->getAttribute('name');
        $function = $compiler->getEnvironment()->getFunction($name);

        $this->setAttribute('name', $name);
        $this->setAttribute('type', 'function');
        $this->setAttribute('thing', $function);
        $this->setAttribute('needs_environment', $function->needsEnvironment());
        $this->setAttribute('needs_context', $function->needsContext());
        $this->setAttribute('arguments', $function->getArguments());
        if ($function instanceof FunctionCallableInterface || $function instanceof SimpleFunction) {
            $callable = $function->getCallable();
            if ('constant' === $name && $this->getAttribute('is_defined_test')) {
                $callable = 'twig_constant_is_defined';
            }

            $this->setAttribute('callable', $callable);
        }
        if ($function instanceof SimpleFunction) {
            $this->setAttribute('is_variadic', $function->isVariadic());
        }

        $this->compileCallable($compiler);
    }
}
