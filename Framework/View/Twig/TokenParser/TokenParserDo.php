<?php

namespace Framework\View\Twig\TokenParser;

use Framework\View\Twig\Node\NodeDo;
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
 * Evaluates an expression, discarding the returned value.
 */
class TokenParserDo extends TokenParser
{
    public function parse(Token $token)
    {
        $expr = $this->parser->getExpressionParser()->parseExpression();

        $this->parser->getStream()->expect(Token::BLOCK_END_TYPE);

        return new NodeDo($expr, $token->getLine(), $this->getTag());
    }

    public function getTag()
    {
        return 'do';
    }
}
