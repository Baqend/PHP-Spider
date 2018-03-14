<?php

namespace Baqend\Component\Spider\AssetHandler;

use Baqend\Component\Spider\Asset;

/**
 * Interface AssetHandlerInterface created on 2018-03-14.
 *
 * @package Baqend\Component\Spider\AssetHandler
 */
interface AssetHandlerInterface
{

    /**
     * Handles a asset and returns a asset to be processed.
     *
     * Handling can be actions like storing a asset to disk or sending it to some web cache.
     * Returns to the spider whether to process
     *
     * @param Asset $asset The asset to handle.
     * @return Asset|null The handled asset that should processed or null, if it should not.
     */
    public function handle(Asset $asset);
}
