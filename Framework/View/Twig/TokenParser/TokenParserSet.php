<?php

namespace Framework\View\Twig\TokenParser;

use Framework\View\Twig\Error\ErrorSyntax;
use Framework\View\Twig\Node\NodeSet;
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
 * Defines a variable.
 *
 * <pre>
 *  {% set foo = 'foo' %}
 *
 *  {% set foo = [1, 2] %}
 *
 *  {% set foo = {'foo': 'bar'} %}
 *
 *  {% set foo = 'foo' ~ 'bar' %}
 *
 *  {% set foo, bar = 'foo', 'bar' %}
 *
 *  {% set foo %}Some content{% endset %}
 * </pre>
 */
class TokenParserSet extends TokenParser
{
    public function parse(Token $token)
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();
        $names = $this->parser->getExpressionParser()->parseAssignmentExpression();

        $capture = false;
        if ($stream->nextIf(Token::OPERATOR_TYPE, '=')) {
            $values = $this->parser->getExpressionParser()->parseMultitargetExpression();

            $stream->expect(Token::BLOCK_END_TYPE);

            if (count($names) !== count($values)) {
                throw new ErrorSyntax('When using set, you must have the same number of variables and assignments.', $stream->getCurrent()->getLine(), $stream->getSourceContext()->getName());
            }
        } else {
            $capture = true;

            if (count($names) > 1) {
                throw new ErrorSyntax('When using set with a block, you cannot have a multi-target.', $stream->getCurrent()->getLine(), $stream->getSourceContext()->getName());
            }

            $stream->expect(Token::BLOCK_END_TYPE);

            $values = $this->parser->subparse(array($this, 'decideBlockEnd'), true);
            $stream->expect(Token::BLOCK_END_TYPE);
        }

        return new NodeSet($capture, $names, $values, $lineno, $this->getTag());
    }

    public function decideBlockEnd(Token $token)
    {
        return $token->test('endset');
    }

    public function getTag()
    {
        return 'set';
    }
}
