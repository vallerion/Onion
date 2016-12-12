<?php

namespace Framework\View\Twig\TokenParser;

use Framework\View\Twig\Node\NodeFlush;
use Framework\View\Twig\Token;
use Framework\View\Twig\TokenParser;

/*
 * This file is part of Twig.
 *
 * (c) 2011 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Flushes the output to the client.
 *
 * @see flush()
 */
class TokenParserFlush extends TokenParser
{
    public function parse(Token $token)
    {
        $this->parser->getStream()->expect(Token::BLOCK_END_TYPE);

        return new NodeFlush($token->getLine(), $this->getTag());
    }

    public function getTag()
    {
        return 'flush';
    }
}
