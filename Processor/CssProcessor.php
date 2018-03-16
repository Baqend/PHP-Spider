<?php

namespace Baqend\Component\Spider\Processor;

use Baqend\Component\Spider\Asset;
use Baqend\Component\Spider\Queue\QueueInterface;
use Baqend\Component\Spider\UrlHelper;

/**
 * Class CssProcessor created on 2018-03-14.
 *
 * @author  Konstantin Simon Maria Möllers
 * @package Baqend\Component\Spider\Processor
 */
class CssProcessor implements ProcessorInterface
{

    /**
     * Checks whether this asset can be processed.
     *
     * @param Asset $asset
     * @return boolean True, if the given asset can be processed by this processor.
     */
    public function canProcess(Asset $asset) {
        return strpos($asset->getContentType(), 'css') !== false || substr($asset->getUrl(), -4) === '.css';
    }

    /**
     * Processes a given asset.
     *
     * Discovered new assets are written to the provided queue.
     *
     * @param Asset $asset The asset to be processed.
     * @param QueueInterface $queue The queue to add new files to.
     * @return Asset The processed asset.
     */
    public function process(Asset $asset, QueueInterface $queue) {
        $cssString = $this->processCss($asset, $asset->getContent(), $queue);

        return $asset->withContent($cssString);
    }

    /**
     * Processed a string containing CSS data.
     *
     * @param Asset $asset The asset containing the CSS.
     * @param string $cssString The CSS to be processed.
     * @param QueueInterface $queue The queue to add new files to.
     * @return string An updated CSS string containing corrected URLs.
     */
    public function processCss(Asset $asset, $cssString, QueueInterface $queue) {
        $patterns = [
            '/url\\(\\s*["\']?([^)"\']+)/', // url()
            '/@import\\s+["\']([^"\']+)/',  // @import
        ];

        $cssString = preg_replace_callback(
            $patterns,
            function (array $matches) use ($asset, $queue) {
                return $this->matchCss($asset, $matches, $queue);
            },
            $cssString
        );

        return $cssString;
    }

    /**
     * Handles matches of URLs within a CSS string.
     *
     * Takes the match, extracts the URL, and adds it to the queue.
     *
     * @param Asset $asset The asset containing the CSS.
     * @param array $matches Array of preg_replace_callback matches.
     * @param QueueInterface $queue The queue to add new files to.
     * @return string An updated string for the text that was originally matched
     */
    private function matchCss(Asset $asset, array $matches, QueueInterface $queue) {
        list($match, $url) = $matches;

        $extractedUrl = UrlHelper::resolve($asset->getUrl(), $url);

        $queue->add(UrlHelper::stripQueryFragment($extractedUrl));
        $match = str_ireplace($url, $extractedUrl, $match);

        return $match;
    }
}
