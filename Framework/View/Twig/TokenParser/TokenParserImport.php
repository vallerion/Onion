<?php

namespace Framework\View\Twig\TokenParser;

use Framework\View\Twig\Node\Expression\NodeExpressionAssignName;
use Framework\View\Twig\Node\NodeImport;
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
 * Imports macros.
 *
 * <pre>
 *   {% import 'forms.html' as forms %}
 * </pre>
 */
class TokenParserImport extends TokenParser
{
    public function parse(Token $token)
    {
        $macro = $this->parser->getExpressionParser()->parseExpression();
        $this->parser->getStream()->expect('as');
        $var = new NodeExpressionAssignName($this->parser->getStream()->expect(Token::NAME_TYPE)->getValue(), $token->getLine());
        $this->parser->getStream()->expect(Token::BLOCK_END_TYPE);

        $this->parser->addImportedSymbol('template', $var->getAttribute('name'));

        return new NodeImport($macro, $var, $token->getLine(), $this->getTag());
    }

    public function getTag()
    {
        return 'import';
    }
}
