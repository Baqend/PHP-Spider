<?php

namespace Baqend\Component\Spider\Downloader;

use Baqend\Component\Spider\Resource;

/**
 * Interface DownloaderInterface created on 2018-03-14.
 *
 * @package Baqend\Component\Spider\Downloader
 */
interface DownloaderInterface
{

    /**
     * Downloads a resource by its URL.
     *
     * @param string $url The URL to download.
     * @return Resource The downloaded resource.
     * @throws DownloaderException When the download did not succeed.
     */
    public function download($url);
}
