<?php

namespace Framework\View\Twig\TokenParser;

use Framework\View\Twig\Node\Expression\NodeExpressionConstant;
use Framework\View\Twig\Node\Expression\NodeExpressionName;
use Framework\View\Twig\Node\NodeEmbed;
use Framework\View\Twig\Token;

/*
 * This file is part of Twig.
 *
 * (c) 2012 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Embeds a template.
 */
class TokenParserEmbed extends TokenParserInclude
{
    public function parse(Token $token)
    {
        $stream = $this->parser->getStream();

        $parent = $this->parser->getExpressionParser()->parseExpression();

        list($variables, $only, $ignoreMissing) = $this->parseArguments();

        $parentToken = $fakeParentToken = new Token(Token::STRING_TYPE, '__parent__', $token->getLine());
        if ($parent instanceof NodeExpressionConstant) {
            $parentToken = new Token(Token::STRING_TYPE, $parent->getAttribute('value'), $token->getLine());
        } elseif ($parent instanceof NodeExpressionName) {
            $parentToken = new Token(Token::NAME_TYPE, $parent->getAttribute('name'), $token->getLine());
        }

        // inject a fake parent to make the parent() function work
        $stream->injectTokens(array(
            new Token(Token::BLOCK_START_TYPE, '', $token->getLine()),
            new Token(Token::NAME_TYPE, 'extends', $token->getLine()),
            $parentToken,
            new Token(Token::BLOCK_END_TYPE, '', $token->getLine()),
        ));

        $module = $this->parser->parse($stream, array($this, 'decideBlockEnd'), true);

        // override the parent with the correct one
        if ($fakeParentToken === $parentToken) {
            $module->setNode('parent', $parent);
        }

        $this->parser->embedTemplate($module);

        $stream->expect(Token::BLOCK_END_TYPE);

        return new NodeEmbed($module->getTemplateName(), $module->getAttribute('index'), $variables, $only, $ignoreMissing, $token->getLine(), $this->getTag());
    }

    public function decideBlockEnd(Token $token)
    {
        return $token->test('endembed');
    }

    public function getTag()
    {
        return 'embed';
    }
}
