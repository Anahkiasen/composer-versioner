<?php

/*
 * This file is part of anahkiasen/composer-versioner
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace ComposerVersioner\Services;

use League\HTMLToMarkdown\HtmlConverter;

/**
 * Converts an array of releases to a Markdown CHANGELOG.
 */
class ChangelogConverter
{
    /**
     * @var array
     */
    protected $releases;

    /**
     * @var string
     */
    protected $description;

    /**
     * @param array       $releases
     * @param string|null $description
     */
    public function __construct(array $releases, $description = null)
    {
        $this->releases = $releases;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getMarkdown()
    {
        $html = '# CHANGELOG';

        $html .= PHP_EOL.PHP_EOL;
        $html .= (new HtmlConverter())->convert($this->description);

        foreach ($this->releases as $release) {
            if (!array_key_exists('changes', $release)) {
                continue;
            }

            $html .= $this->convertRelease($release);
        }

        return $html;
    }

    /**
     * @param array $release
     *
     * @return string
     */
    protected function convertRelease(array $release)
    {
        $html = '';
        $html .= PHP_EOL.PHP_EOL;
        $html .= '## '.$release['name'].' - '.$release['date'];

        foreach ($release['changes'] as $section => $changes) {
            if (!$changes) {
                continue;
            }

            $html .= PHP_EOL.PHP_EOL;
            $html .= '### '.ucfirst($section);
            $html .= PHP_EOL.PHP_EOL;

            foreach ($changes as $key => $change) {
                $changes[$key] = '- '.(new HtmlConverter())->convert($change);
            }

            $html .= implode(PHP_EOL, $changes);
        }

        return $html;
    }
}
