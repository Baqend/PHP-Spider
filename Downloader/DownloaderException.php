<?php

namespace Baqend\Component\Spider\Downloader;

use Baqend\Component\Spider\UrlException;

/**
 * Class DownloaderException created on 2018-03-14.
 *
 * @author  Konstantin Simon Maria Möllers
 * @package Baqend\Component\Spider\Downloader
 */
class DownloaderException extends UrlException
{

    /**
     * DownloaderException constructor.
     *
     * @param string $url The URL which caused this exception.
     * @param \Exception $previous The previous throwable used for the exception chaining.
     */
    public function __construct($url, \Exception $previous) {
        $previousMessage = $previous->getMessage();
        $message = "Cannot download $url: $previousMessage";

        parent::__construct($url, $message, 0, $previous);
    }
}
