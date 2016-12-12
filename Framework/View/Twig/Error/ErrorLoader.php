<?php

namespace Framework\View\Twig\Error;

use Exception;
use Framework\View\Twig\Error;

/*
 * This file is part of Twig.
 *
 * (c) 2010 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Exception thrown when an error occurs during template loading.
 *
 * Automatic template information guessing is always turned off as
 * if a template cannot be loaded, there is nothing to guess.
 * However, when a template is loaded from another one, then, we need
 * to find the current context and this is automatically done by
 * Twig_Template::displayWithErrorHandling().
 *
 * This strategy makes Environment::resolveTemplate() much faster.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ErrorLoader extends Error
{
    public function __construct($message, $lineno = -1, $filename = null, Exception $previous = null)
    {
        parent::__construct($message, false, false, $previous);
    }
}
