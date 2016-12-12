<?php

namespace Framework\View\Twig\TokenParser;

use Framework\View\Twig\Node\NodeWith;
use Framework\View\Twig\Token;
use Framework\View\Twig\TokenParser;

/*
 * This file is part of Twig.
 *
 * (c) 2016 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Creates a nested scope.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class TokenParserWith extends TokenParser
{
    public function parse(Token $token)
    {
        $stream = $this->parser->getStream();

        $variables = null;
        $only = false;
        if (!$stream->test(Token::BLOCK_END_TYPE)) {
            $variables = $this->parser->getExpressionParser()->parseExpression();
            $only = $stream->nextIf(Token::NAME_TYPE, 'only');
        }

        $stream->expect(Token::BLOCK_END_TYPE);

        $body = $this->parser->subparse(array($this, 'decideWithEnd'), true);

        $stream->expect(Token::BLOCK_END_TYPE);

        return new NodeWith($body, $variables, $only, $token->getLine(), $this->getTag());
    }

    public function decideWithEnd(Token $token)
    {
        return $token->test('endwith');
    }

    public function getTag()
    {
        return 'with';
    }
}
