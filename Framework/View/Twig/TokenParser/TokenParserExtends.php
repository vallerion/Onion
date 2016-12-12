<?php

namespace Framework\View\Twig\TokenParser;

use Framework\View\Twig\Error\ErrorSyntax;
use Framework\View\Twig\Token;
use Framework\View\Twig\TokenParser;

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 * (c) 2009 Armin Ronacher
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Extends a template by another one.
 *
 * <pre>
 *  {% extends "base.html" %}
 * </pre>
 */
class TokenParserExtends extends TokenParser
{
    public function parse(Token $token)
    {
        $stream = $this->parser->getStream();

        if (!$this->parser->isMainScope()) {
            throw new ErrorSyntax('Cannot extend from a block.', $token->getLine(), $stream->getSourceContext()->getName());
        }

        if (null !== $this->parser->getParent()) {
            throw new ErrorSyntax('Multiple extends tags are forbidden.', $token->getLine(), $stream->getSourceContext()->getName());
        }
        $this->parser->setParent($this->parser->getExpressionParser()->parseExpression());

        $stream->expect(Token::BLOCK_END_TYPE);
    }

    public function getTag()
    {
        return 'extends';
    }
}
