<?php

namespace Baqend\Component\Spider\ResourceHandler;

use Baqend\Component\Spider\Resource;

/**
 * Interface ResourceHandlerInterface created on 2018-03-14.
 *
 * @package Baqend\Component\Spider\ResourceHandler
 */
interface ResourceHandlerInterface
{

    /**
     * Handles a resource and returns a resource to be processed.
     *
     * Handling can be actions like storing a resource to disk or sending it to some web cache.
     * Returns to the spider whether to process
     *
     * @param Resource $resource The resource to handle.
     * @return Resource|null The handled resource that should processed or null, if it should not.
     */
    public function handle(Resource $resource);
}
