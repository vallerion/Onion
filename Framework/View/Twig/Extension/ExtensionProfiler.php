<?php

namespace Framework\View\Twig\Extension;

use Framework\View\Twig\Extension;
use Framework\View\Twig\Profiler\NodeVisitor\ProfilerNodeVisitorProfiler;
use Framework\View\Twig\Profiler\ProfilerProfile;

/*
 * This file is part of Twig.
 *
 * (c) 2015 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class ExtensionProfiler extends Extension
{
    private $actives = array();

    public function __construct(ProfilerProfile $profile)
    {
        $this->actives[] = $profile;
    }

    public function enter(ProfilerProfile $profile)
    {
        $this->actives[0]->addProfile($profile);
        array_unshift($this->actives, $profile);
    }

    public function leave(ProfilerProfile $profile)
    {
        $profile->leave();
        array_shift($this->actives);

        if (1 === count($this->actives)) {
            $this->actives[0]->leave();
        }
    }

    public function getNodeVisitors()
    {
        return array(new ProfilerNodeVisitorProfiler(get_class($this)));
    }

    public function getName()
    {
        return 'profiler';
    }
}
