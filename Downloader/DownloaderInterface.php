<?php

namespace Baqend\Component\Spider\Downloader;

use Baqend\Component\Spider\Asset;

/**
 * Interface DownloaderInterface created on 2018-03-14.
 *
 * @package Baqend\Component\Spider\Downloader
 */
interface DownloaderInterface
{

    /**
     * Downloads an asset by its URL.
     *
     * @param string $url The URL to download.
     * @return Asset The downloaded asset.
     * @throws DownloaderException When the download did not succeed.
     */
    public function download($url);
}
