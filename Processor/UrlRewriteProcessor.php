<?php

namespace Baqend\Component\Spider\Processor;

use Baqend\Component\Spider\Asset;
use Baqend\Component\Spider\Queue\QueueInterface;

/**
 * This processor can replace the prefix of an asset's URL with another string.
 *
 * Class UrlRewriteProcessor created on 2018-03-15.
 *
 * @author  Konstantin Simon Maria Möllers
 * @package Baqend\Component\Spider\Processor
 */
class UrlRewriteProcessor implements ProcessorInterface
{

    private $search;
    private $replace;

    /**
     * UrlRewriteProcessor constructor.
     *
     * @param string $search The URL prefix to search for.
     * @param string $replace The replacement for that URL part, if it matches.
     */
    public function __construct($search, $replace) {
        $this->search = $search;
        $this->replace = $replace;
    }


    /**
     * Checks whether this asset can be processed.
     *
     * @param Asset $asset
     * @return boolean True, if the given asset can be processed by this processor.
     */
    public function canProcess(Asset $asset) {
        return strpos($asset->getUrl(), $this->search) === 0;
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
        return $this->replaceUrl($asset);
    }

    /**
     * Replaces the URL of an asset, if it matches given criteria.
     *
     * @param Asset $asset The asset whose URL has to match.
     * @return Asset The replaced asset or $asset, if nothing has been changed.
     */
    public function replaceUrl(Asset $asset) {
        if (strpos($asset->getUrl(), $this->search) === 0) {
            return $asset->withUrl(str_replace($this->search, $this->replace, $asset->getUrl()));
        }

        return $asset;
    }
}
