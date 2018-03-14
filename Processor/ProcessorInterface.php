<?php

namespace Baqend\Component\Spider\Processor;

use Baqend\Component\Spider\Queue\QueueInterface;
use Baqend\Component\Spider\Asset;

/**
 * Interface ProcessorInterface created on 2018-03-14.
 *
 * @package Baqend\Component\Spider\Processor
 */
interface ProcessorInterface
{

    /**
     * Checks whether this asset can be processed.
     *
     * @param Asset $asset
     * @return boolean True, if the given asset can be processed by this processor.
     */
    public function canProcess(Asset $asset);

    /**
     * Processes a given asset.
     *
     * Discovered new assets are written to the provided queue.
     *
     * @param Asset $asset The asset to be processed.
     * @param QueueInterface $queue The queue to add new files to.
     * @return void
     */
    public function process(Asset $asset, QueueInterface $queue);
}
