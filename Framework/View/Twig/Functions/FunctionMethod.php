<?php

namespace Framework\View\Twig\Functions;

use Framework\View\Twig\MainFunction;
use Framework\View\Twig\ExtensionInterface;

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 * (c) 2010 Arnaud Le Blanc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//@trigger_error('The Twig_Function_Method class is deprecated since version 1.12 and will be removed in 2.0. Use Twig_SimpleFunction instead.', E_USER_DEPRECATED);

/**
 * Represents a method template function.
 *
 * Use Twig_SimpleFunction instead.
 *
 * @author Arnaud Le Blanc <arnaud.lb@gmail.com>
 *
 * @deprecated since 1.12 (to be removed in 2.0)
 */
class FunctionMethod extends MainFunction
{
    protected $extension;
    protected $method;

    public function __construct(ExtensionInterface $extension, $method, array $options = array())
    {
        $options['callable'] = array($extension, $method);

        parent::__construct($options);

        $this->extension = $extension;
        $this->method = $method;
    }

    public function compile()
    {
        return sprintf('$this->env->getExtension(\'%s\')->%s', get_class($this->extension), $this->method);
    }
}
