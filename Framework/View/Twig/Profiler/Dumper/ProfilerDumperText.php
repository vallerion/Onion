<?php

namespace Framework\View\Twig\Profiler\Dumper;

use Framework\View\Twig\Profiler\ProfilerProfile;

/*
 * This file is part of Twig.
 *
 * (c) 2015 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ProfilerDumperText
{
    private $root;

    public function dump(ProfilerProfile $profile)
    {
        return $this->dumpProfile($profile);
    }

    protected function formatTemplate(ProfilerProfile $profile, $prefix)
    {
        return sprintf('%s└ %s', $prefix, $profile->getTemplate());
    }

    protected function formatNonTemplate(ProfilerProfile $profile, $prefix)
    {
        return sprintf('%s└ %s::%s(%s)', $prefix, $profile->getTemplate(), $profile->getType(), $profile->getName());
    }

    protected function formatTime(ProfilerProfile $profile, $percent)
    {
        return sprintf('%.2fms/%.0f%%', $profile->getDuration() * 1000, $percent);
    }

    private function dumpProfile(ProfilerProfile $profile, $prefix = '', $sibling = false)
    {
        if ($profile->isRoot()) {
            $this->root = $profile->getDuration();
            $start = $profile->getName();
        } else {
            if ($profile->isTemplate()) {
                $start = $this->formatTemplate($profile, $prefix);
            } else {
                $start = $this->formatNonTemplate($profile, $prefix);
            }
            $prefix .= $sibling ? '│ ' : '  ';
        }

        $percent = $this->root ? $profile->getDuration() / $this->root * 100 : 0;

        if ($profile->getDuration() * 1000 < 1) {
            $str = $start."\n";
        } else {
            $str = sprintf("%s %s\n", $start, $this->formatTime($profile, $percent));
        }

        $nCount = count($profile->getProfiles());
        foreach ($profile as $i => $p) {
            $str .= $this->dumpProfile($p, $prefix, $i + 1 !== $nCount);
        }

        return $str;
    }
}
