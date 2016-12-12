<?php

namespace Framework\View\Twig\TokenParser;

use Framework\View\Twig\Error\ErrorSyntax;
use Framework\View\Twig\Node\NodeBody;
use Framework\View\Twig\Node\NodeMacro;
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
 * Defines a macro.
 *
 * <pre>
 * {% macro input(name, value, type, size) %}
 *    <input type="{{ type|default('text') }}" name="{{ name }}" value="{{ value|e }}" size="{{ size|default(20) }}" />
 * {% endmacro %}
 * </pre>
 */
class TokenParserMacro extends TokenParser
{
    public function parse(Token $token)
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();
        $name = $stream->expect(Token::NAME_TYPE)->getValue();

        $arguments = $this->parser->getExpressionParser()->parseArguments(true, true);

        $stream->expect(Token::BLOCK_END_TYPE);
        $this->parser->pushLocalScope();
        $body = $this->parser->subparse(array($this, 'decideBlockEnd'), true);
        if ($token = $stream->nextIf(Token::NAME_TYPE)) {
            $value = $token->getValue();

            if ($value != $name) {
                throw new ErrorSyntax(sprintf('Expected endmacro for macro "%s" (but "%s" given).', $name, $value), $stream->getCurrent()->getLine(), $stream->getSourceContext()->getName());
            }
        }
        $this->parser->popLocalScope();
        $stream->expect(Token::BLOCK_END_TYPE);

        $this->parser->setMacro($name, new NodeMacro($name, new NodeBody(array($body)), $arguments, $lineno, $this->getTag()));
    }

    public function decideBlockEnd(Token $token)
    {
        return $token->test('endmacro');
    }

    public function getTag()
    {
        return 'macro';
    }
}
