<?php

namespace Baqend\Component\Spider\Queue;

/**
 * A last in first out (LIFO) queue to traverse a webpage depth-first.
 *
 * Class DepthQueue created on 2018-03-14.
 *
 * @author  Konstantin Simon Maria Möllers
 * @package Baqend\Component\Spider\Queue
 */
class DepthQueue implements QueueInterface
{

    private $added = [];
    private $queue = [];

    /**
     * Adds an URL to process to the queue.
     *
     * @param string $url The URL to add.
     * @return boolean True, if the URL was added or false, if the URL has been queued before.
     */
    public function add($url) {
        if (isset($this->added[$url])) {
            return false;
        }

        $this->added[$url] = true;
        $this->queue[] = $url;

        return true;
    }

    /**
     * Returns the next URL to process.
     *
     * @return string|null The next URL or null, if no URLs to process are left.
     */
    public function next() {
        return array_pop($this->queue);
    }
}
