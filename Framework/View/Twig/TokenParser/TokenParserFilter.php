<?php

namespace Framework\View\Twig\TokenParser;

use Framework\View\Twig\Node\Expression\NodeExpressionBlockReference;
use Framework\View\Twig\Node\Expression\NodeExpressionConstant;
use Framework\View\Twig\Node\NodeBlock;
use Framework\View\Twig\Node\NodePrint;
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
 * Filters a section of a template by applying filters.
 *
 * <pre>
 * {% filter upper %}
 *  This text becomes uppercase
 * {% endfilter %}
 * </pre>
 */
class TokenParserFilter extends TokenParser
{
    public function parse(Token $token)
    {
        $name = $this->parser->getVarName();
        $ref = new NodeExpressionBlockReference(new NodeExpressionConstant($name, $token->getLine()), null, $token->getLine(), $this->getTag());

        $filter = $this->parser->getExpressionParser()->parseFilterExpressionRaw($ref, $this->getTag());
        $this->parser->getStream()->expect(Token::BLOCK_END_TYPE);

        $body = $this->parser->subparse(array($this, 'decideBlockEnd'), true);
        $this->parser->getStream()->expect(Token::BLOCK_END_TYPE);

        $block = new NodeBlock($name, $body, $token->getLine());
        $this->parser->setBlock($name, $block);

        return new NodePrint($filter, $token->getLine(), $this->getTag());
    }

    public function decideBlockEnd(Token $token)
    {
        return $token->test('endfilter');
    }

    public function getTag()
    {
        return 'filter';
    }
}
