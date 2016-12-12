<?php

namespace Framework\View\Twig;

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Interface implemented by lexer classes.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @deprecated since 1.12 (to be removed in 3.0)
 */
interface LexerInterface
{
    /**
     * Tokenizes a source code.
     *
     * @param string|Source $code The source code
     * @param string             $name A unique identifier for the source code
     *
     * @return TokenStream
     *
     * @throws ErrorSyntax When the code is syntactically wrong
     */
    public function tokenize($code, $name = null);
}
