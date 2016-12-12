<?php

namespace Framework\View\Twig\TokenParser;

use Framework\View\Twig\Error\ErrorSyntax;
use Framework\View\Twig\Node\NodeInclude;
use Framework\View\Twig\Node\NodeSandbox;
use Framework\View\Twig\Node\NodeText;
use Framework\View\Twig\Token;
use Framework\View\Twig\TokenParser;

/*
 * This file is part of Twig.
 *
 * (c) 2010 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Marks a section of a template as untrusted code that must be evaluated in the sandbox mode.
 *
 * <pre>
 * {% sandbox %}
 *     {% include 'user.html' %}
 * {% endsandbox %}
 * </pre>
 *
 * @see http://www.twig-project.org/doc/api.html#sandbox-extension for details
 */
class TokenParserSandbox extends TokenParser
{
    public function parse(Token $token)
    {
        $stream = $this->parser->getStream();
        $stream->expect(Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse(array($this, 'decideBlockEnd'), true);
        $stream->expect(Token::BLOCK_END_TYPE);

        // in a sandbox tag, only include tags are allowed
        if (!$body instanceof NodeInclude) {
            foreach ($body as $node) {
                if ($node instanceof NodeText && ctype_space($node->getAttribute('data'))) {
                    continue;
                }

                if (!$node instanceof NodeInclude) {
                    throw new ErrorSyntax('Only "include" tags are allowed within a "sandbox" section.', $node->getTemplateLine(), $stream->getSourceContext()->getName());
                }
            }
        }

        return new NodeSandbox($body, $token->getLine(), $this->getTag());
    }

    public function decideBlockEnd(Token $token)
    {
        return $token->test('endsandbox');
    }

    public function getTag()
    {
        return 'sandbox';
    }
}
