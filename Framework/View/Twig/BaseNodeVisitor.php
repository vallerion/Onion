<?php

namespace Framework\View\Twig;

use LogicException;

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * BaseNodeVisitor can be used to make node visitors compatible with Twig 1.x and 2.x.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
abstract class BaseNodeVisitor implements NodeVisitorInterface
{
    /**
     * {@inheritdoc}
     */
    final public function enterNode(NodeInterface $node, Environment $env)
    {
        if (!$node instanceof Node) {
            throw new LogicException('BaseNodeVisitor only supports Node instances.');
        }

        return $this->doEnterNode($node, $env);
    }

    /**
     * {@inheritdoc}
     */
    final public function leaveNode(NodeInterface $node, Environment $env)
    {
        if (!$node instanceof Node) {
            throw new LogicException('BaseNodeVisitor only supports Node instances.');
        }

        return $this->doLeaveNode($node, $env);
    }

    /**
     * Called before child nodes are visited.
     *
     * @return Node The modified node
     */
    abstract protected function doEnterNode(Node $node, Environment $env);

    /**
     * Called after child nodes are visited.
     *
     * @return Node|false The modified node or false if the node must be removed
     */
    abstract protected function doLeaveNode(Node $node, Environment $env);
}
