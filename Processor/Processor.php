<?php

namespace Baqend\Component\Spider\Processor;

use Baqend\Component\Spider\Asset;
use Baqend\Component\Spider\Queue\QueueInterface;

/**
 * This is a processor which calls type-specific other processors.
 *
 * Each processor added to this aggregate processor is asked after each
 * other if it can process the given asset. The processed asset is then
 * passed on to the next processor to handle it.
 *
 * Class Processor created on 2018-03-16.
 *
 * @author  Konstantin Simon Maria Möllers
 * @package Baqend\Component\Spider\Processor
 */
class Processor implements AggregateProcessorInterface
{

    /**
     * @var ProcessorInterface[]
     */
    private $processors = [];

    /**
     * Checks whether this asset can be processed.
     *
     * @param Asset $asset
     * @return boolean True, if the given asset can be processed by this processor.
     */
    public function canProcess(Asset $asset) {
        foreach ($this->processors as $processor) {
            if ($processor->canProcess($asset)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Processes a given asset.
     *
     * Discovered new assets are written to the provided queue.
     *
     * @param Asset $asset The asset to be processed.
     * @param QueueInterface $queue The queue to add new files to.
     * @return Asset The processed asset.
     * @throws InputOutputException When I/O operations during processing fail.
     */
    public function process(Asset $asset, QueueInterface $queue) {
        foreach ($this->processors as $processor) {
            if ($processor->canProcess($asset)) {
                $asset = $processor->process($asset, $queue);
            }
        }

        return $asset;
    }

    /**
     * Adds a processor to the list of asked processors.
     *
     * @param ProcessorInterface $processor The processor to add.
     * @return static This method is chainable.
     */
    public function addProcessor(ProcessorInterface $processor) {
        $this->processors[] = $processor;

        return $this;
    }

    /**
     * Removes a processor from the list of asked processors.
     *
     * @param ProcessorInterface $processor The processor to remove.
     * @return boolean True, if the processor has been removed.
     */
    public function removeProcessor(ProcessorInterface $processor) {
        $key = array_search($processor, $this->processors, true);
        if ($key === false) {
            return false;
        }

        unset($this->processors[$key]);
        $this->processors = array_values($this->processors);

        return true;
    }

    /**
     * Checks if a processor is on the list of asked processors.
     *
     * @param ProcessorInterface $processor The processor to check.
     * @return boolean True, if the processor is being asked.
     */
    public function hasProcessor(ProcessorInterface $processor) {
        return array_search($processor, $this->processors, true) !== false;
    }
}
