<?php

namespace Framework\View\Twig\Test;

use Framework\View\Twig\ExtensionInterface;
use Framework\View\Twig\Test;

/*
 * This file is part of Twig.
 *
 * (c) 2010 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//@trigger_error('The Test_Method class is deprecated since version 1.12 and will be removed in 2.0. Use SimpleTest instead.', E_USER_DEPRECATED);


/**
 * Represents a method template test.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @deprecated since 1.12 (to be removed in 2.0)
 */
class TestMethod extends Test
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
