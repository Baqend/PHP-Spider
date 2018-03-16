<?php

namespace Baqend\Component\Spider\UrlHandler;

/**
 * URL handling is used to decide whether a URL from the queue needs
 * further processing or not. An unhandled URL should throw no error
 * but be silently ignored.
 *
 * Interface UrlHandlerInterface created on 2018-03-14.
 *
 * @package Baqend\Component\Spider\UrlHandler
 */
interface UrlHandlerInterface
{

    /**
     * Handles a URL and returns if it should be further processed.
     *
     * @param string $url The URL to handle.
     * @return boolean True, if asset should be processed or false, if it should not.
     */
    public function handle($url);
}
