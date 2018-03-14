<?php

namespace Baqend\Component\Spider\Processor;

use Baqend\Component\Spider\Queue\QueueInterface;
use Baqend\Component\Spider\Resource;

/**
 * Interface ProcessorInterface created on 2018-03-14.
 *
 * @package Baqend\Component\Spider\Processor
 */
interface ProcessorInterface
{

    /**
     * Checks whether this resource can be processed.
     *
     * @param Resource $resource
     * @return boolean True, if the given resource can be processed by this processor.
     */
    public function canProcess(Resource $resource);

    /**
     * Processes a given resource.
     *
     * Discovered new resources are written to the provided queue.
     *
     * @param Resource $resource The resource to be processed.
     * @param QueueInterface $queue The queue to add new files to.
     * @return void
     */
    public function process(Resource $resource, QueueInterface $queue);
}
