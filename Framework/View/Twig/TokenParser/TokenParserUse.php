<?php

namespace Framework\View\Twig\TokenParser;

use Framework\View\Twig\Error\ErrorSyntax;
use Framework\View\Twig\Node;
use Framework\View\Twig\Node\Expression\NodeExpressionConstant;
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
 * Imports blocks defined in another template into the current template.
 *
 * <pre>
 * {% extends "base.html" %}
 *
 * {% use "blocks.html" %}
 *
 * {% block title %}{% endblock %}
 * {% block content %}{% endblock %}
 * </pre>
 *
 * @see http://www.twig-project.org/doc/templates.html#horizontal-reuse for details.
 */
class TokenParserUse extends TokenParser
{
    public function parse(Token $token)
    {
        $template = $this->parser->getExpressionParser()->parseExpression();
        $stream = $this->parser->getStream();

        if (!$template instanceof NodeExpressionConstant) {
            throw new ErrorSyntax('The template references in a "use" statement must be a string.', $stream->getCurrent()->getLine(), $stream->getSourceContext()->getName());
        }

        $targets = array();
        if ($stream->nextIf('with')) {
            do {
                $name = $stream->expect(Token::NAME_TYPE)->getValue();

                $alias = $name;
                if ($stream->nextIf('as')) {
                    $alias = $stream->expect(Token::NAME_TYPE)->getValue();
                }

                $targets[$name] = new NodeExpressionConstant($alias, -1);

                if (!$stream->nextIf(Token::PUNCTUATION_TYPE, ',')) {
                    break;
                }
            } while (true);
        }

        $stream->expect(Token::BLOCK_END_TYPE);

        $this->parser->addTrait(new Node(array('template' => $template, 'targets' => new Node($targets))));
    }

    public function getTag()
    {
        return 'use';
    }
}
