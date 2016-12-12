<?php

namespace Framework\View\Twig\Util;

use IteratorIterator;

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class UtilTemplateDirIterator extends IteratorIterator
{
    public function current()
    {
        return file_get_contents(parent::current());
    }

    public function key()
    {
        return (string) parent::key();
    }
}
