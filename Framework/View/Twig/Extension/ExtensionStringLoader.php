<?php

namespace Framework\View\Twig\Extension;

use Framework\View\Twig\Environment;
use Framework\View\Twig\Extension;
use Framework\View\Twig\SimpleFunction;

/*
 * This file is part of Twig.
 *
 * (c) 2012 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class ExtensionStringLoader extends Extension
{
    public function getFunctions()
    {
        return array(
            new SimpleFunction('template_from_string', 'twig_template_from_string', array('needs_environment' => true)),
        );
    }

    public function getName()
    {
        return 'string_loader';
    }
}

/**
 * Loads a template from a string.
 *
 * <pre>
 * {{ include(template_from_string("Hello {{ name }}")) }}
 * </pre>
 *
 * @param Environment $env      A Environment instance
 * @param string           $template A template as a string or object implementing __toString()
 *
 * @return Template
 */
function twig_template_from_string(Environment $env, $template)
{
    return $env->createTemplate((string) $template);
}
