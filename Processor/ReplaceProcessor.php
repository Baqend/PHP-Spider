<?php

namespace Baqend\Component\Spider\Processor;

use Baqend\Component\Spider\Asset;
use Baqend\Component\Spider\Queue\QueueInterface;

/**
 * This processor can replace strings within an assets content with an replacement.
 *
 * Class ReplaceProcessor created on 2018-03-15.
 *
 * @author  Konstantin Simon Maria Möllers
 * @package Baqend\Component\Spider\Processor
 */
class ReplaceProcessor implements ProcessorInterface
{

    private $search;
    private $replace;

    /**
     * ReplaceProcessor constructor.
     *
     * @param string $search The search string prefix to search for.
     * @param string $replace The replacement for that search string part, if it matches.
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
        return true;
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
        return $this->replaceContent($asset);
    }

    /**
     * Replaces the search string within the content of an asset.
     *
     * @param Asset $asset The asset whose content will be replaced.
     * @return Asset The asset with replaced content.
     */
    public function replaceContent(Asset $asset) {
        $content = str_replace($this->search, $this->replace, $asset->getContent());

        return $asset->withContent($content);
    }
}
