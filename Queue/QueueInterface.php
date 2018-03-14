<?php

namespace Baqend\Component\Spider\Queue;

/**
 * Interface QueueInterface created on 2018-03-14.
 *
 * @author  Konstantin Simon Maria Möllers
 * @package Baqend\Component\Spider\Queue
 */
interface QueueInterface
{

    /**
     * Adds an URL to process to the queue.
     *
     * @param string $url The URL to add.
     * @return boolean True, if the URL was added or false, if the URL has been queued before.
     */
    public function add($url);

    /**
     * Returns the next URL to process.
     *
     * @return string|null The next URL or null, if no URLs to process are left.
     */
    public function next();
}
