<?php

namespace Baqend\Component\Spider\Processor;

/**
 * Interface AggregateProcessorInterface created on 2018-03-16.
 *
 * @package Baqend\Component\Spider\Processor
 */
interface AggregateProcessorInterface extends ProcessorInterface
{

    /**
     * Adds a processor to the list of asked processors.
     *
     * @param ProcessorInterface $processor The processor to add.
     * @return static This method is chainable.
     */
    public function addProcessor(ProcessorInterface $processor);

    /**
     * Removes a processor from the list of asked processors.
     *
     * @param ProcessorInterface $processor The processor to remove.
     * @return boolean True, if the processor has been removed.
     */
    public function removeProcessor(ProcessorInterface $processor);

    /**
     * Checks if a processor is on the list of asked processors.
     *
     * @param ProcessorInterface $processor The processor to check.
     * @return boolean True, if the processor is being asked.
     */
    public function hasProcessor(ProcessorInterface $processor);
}
