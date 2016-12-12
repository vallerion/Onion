<?php

namespace Framework\View\Twig\TokenParser;

use Framework\View\Twig\Error\ErrorSyntax;
use Framework\View\Twig\Node\Expression\NodeExpressionConstant;
use Framework\View\Twig\Node\NodeAutoEscape;
use Framework\View\Twig\Token;
use Framework\View\Twig\TokenParser;

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


/**
 * Marks a section of a template to be escaped or not.
 *
 * <pre>
 * {% autoescape true %}
 *   Everything will be automatically escaped in this block
 * {% endautoescape %}
 *
 * {% autoescape false %}
 *   Everything will be outputed as is in this block
 * {% endautoescape %}
 *
 * {% autoescape true js %}
 *   Everything will be automatically escaped in this block
 *   using the js escaping strategy
 * {% endautoescape %}
 * </pre>
 */
class TokenParserAutoEscape extends TokenParser
{
    public function parse(Token $token)
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();

        if ($stream->test(Token::BLOCK_END_TYPE)) {
            $value = 'html';
        } else {
            $expr = $this->parser->getExpressionParser()->parseExpression();
            if (!$expr instanceof NodeExpressionConstant) {
                throw new ErrorSyntax('An escaping strategy must be a string or a bool.', $stream->getCurrent()->getLine(), $stream->getSourceContext()->getName());
            }
            $value = $expr->getAttribute('value');

            $compat = true === $value || false === $value;

            if (true === $value) {
                $value = 'html';
            }

            if ($compat && $stream->test(Token::NAME_TYPE)) {
                @trigger_error('Using the autoescape tag with "true" or "false" before the strategy name is deprecated since version 1.21.', E_USER_DEPRECATED);

                if (false === $value) {
                    throw new ErrorSyntax('Unexpected escaping strategy as you set autoescaping to false.', $stream->getCurrent()->getLine(), $stream->getSourceContext()->getName());
                }

                $value = $stream->next()->getValue();
            }
        }

        $stream->expect(Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse(array($this, 'decideBlockEnd'), true);
        $stream->expect(Token::BLOCK_END_TYPE);

        return new NodeAutoEscape($value, $body, $lineno, $this->getTag());
    }

    public function decideBlockEnd(Token $token)
    {
        return $token->test('endautoescape');
    }

    public function getTag()
    {
        return 'autoescape';
    }
}
