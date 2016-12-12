<?php

namespace Framework\View\Twig\Loader;

use Framework\View\Twig\Error\ErrorLoader;
use Framework\View\Twig\ExistsLoaderInterface;
use Framework\View\Twig\LoaderInterface;
use Framework\View\Twig\Source;
use Framework\View\Twig\SourceContextLoaderInterface;

/*
 * This file is part of Twig.
 *
 * (c) 2011 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Loads templates from other loaders.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class LoaderChain implements LoaderInterface, ExistsLoaderInterface, SourceContextLoaderInterface
{
    private $hasSourceCache = array();
    protected $loaders = array();

    /**
     * @param LoaderInterface[] $loaders
     */
    public function __construct(array $loaders = array())
    {
        foreach ($loaders as $loader) {
            $this->addLoader($loader);
        }
    }

    public function addLoader(LoaderInterface $loader)
    {
        $this->loaders[] = $loader;
        $this->hasSourceCache = array();
    }

    /**
     * {@inheritdoc}
     */
    public function getSource($name)
    {
        @trigger_error(sprintf('Calling "getSource" on "%s" is deprecated since 1.27. Use getSourceContext() instead.', get_class($this)), E_USER_DEPRECATED);

        $exceptions = array();
        foreach ($this->loaders as $loader) {
            if ($loader instanceof ExistsLoaderInterface && !$loader->exists($name)) {
                continue;
            }

            try {
                return $loader->getSource($name);
            } catch (ErrorLoader $e) {
                $exceptions[] = $e->getMessage();
            }
        }

        throw new ErrorLoader(sprintf('Template "%s" is not defined%s.', $name, $exceptions ? ' ('.implode(', ', $exceptions).')' : ''));
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceContext($name)
    {
        $exceptions = array();
        foreach ($this->loaders as $loader) {
            if ($loader instanceof ExistsLoaderInterface && !$loader->exists($name)) {
                continue;
            }

            try {
                if ($loader instanceof SourceContextLoaderInterface) {
                    return $loader->getSourceContext($name);
                }

                return new Source($loader->getSource($name), $name);
            } catch (ErrorLoader $e) {
                $exceptions[] = $e->getMessage();
            }
        }

        throw new ErrorLoader(sprintf('Template "%s" is not defined%s.', $name, $exceptions ? ' ('.implode(', ', $exceptions).')' : ''));
    }

    /**
     * {@inheritdoc}
     */
    public function exists($name)
    {
        $name = (string) $name;

        if (isset($this->hasSourceCache[$name])) {
            return $this->hasSourceCache[$name];
        }

        foreach ($this->loaders as $loader) {
            if ($loader instanceof ExistsLoaderInterface) {
                if ($loader->exists($name)) {
                    return $this->hasSourceCache[$name] = true;
                }

                continue;
            }

            try {
                if ($loader instanceof SourceContextLoaderInterface) {
                    $loader->getSourceContext($name);
                } else {
                    $loader->getSource($name);
                }

                return $this->hasSourceCache[$name] = true;
            } catch (ErrorLoader $e) {
            }
        }

        return $this->hasSourceCache[$name] = false;
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheKey($name)
    {
        $exceptions = array();
        foreach ($this->loaders as $loader) {
            if ($loader instanceof ExistsLoaderInterface && !$loader->exists($name)) {
                continue;
            }

            try {
                return $loader->getCacheKey($name);
            } catch (ErrorLoader $e) {
                $exceptions[] = get_class($loader).': '.$e->getMessage();
            }
        }

        throw new ErrorLoader(sprintf('Template "%s" is not defined%s.', $name, $exceptions ? ' ('.implode(', ', $exceptions).')' : ''));
    }

    /**
     * {@inheritdoc}
     */
    public function isFresh($name, $time)
    {
        $exceptions = array();
        foreach ($this->loaders as $loader) {
            if ($loader instanceof ExistsLoaderInterface && !$loader->exists($name)) {
                continue;
            }

            try {
                return $loader->isFresh($name, $time);
            } catch (ErrorLoader $e) {
                $exceptions[] = get_class($loader).': '.$e->getMessage();
            }
        }

        throw new ErrorLoader(sprintf('Template "%s" is not defined%s.', $name, $exceptions ? ' ('.implode(', ', $exceptions).')' : ''));
    }
}
