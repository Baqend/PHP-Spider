<?php

namespace Baqend\Component\Spider;

use Baqend\Component\Spider\Downloader\DownloaderException;
use Baqend\Component\Spider\Downloader\DownloaderInterface;
use Baqend\Component\Spider\Processor\ProcessorInterface;
use Baqend\Component\Spider\Processor\UnprocessableException;
use Baqend\Component\Spider\Queue\QueueInterface;
use Baqend\Component\Spider\UrlHandler\UrlHandlerInterface;

/**
 * Class Spider created on 2018-03-14.
 *
 * @author  Konstantin Simon Maria Möllers
 * @package Baqend\Component\Spider
 */
class Spider
{

    /**
     * @var QueueInterface
     */
    private $queue;

    /**
     * @var DownloaderInterface
     */
    private $downloader;

    /**
     * @var UrlHandlerInterface|null
     */
    private $urlHandler;

    /**
     * @var ProcessorInterface|null
     */
    private $processor;

    /**
     * Spider constructor.
     *
     * @param QueueInterface $queue A queue to store URLs to download and process.
     * @param DownloaderInterface$downloader A downloader to download URLs and provide assets.
     * @param UrlHandlerInterface|null $urlHandler An optional handler for URLs to download.
     * @param ProcessorInterface|null $processor An optional processor for downloaded files.
     */
    public function __construct(
        QueueInterface $queue,
        DownloaderInterface $downloader,
        UrlHandlerInterface $urlHandler = null,
        ProcessorInterface $processor = null
    ) {
        $this->queue = $queue;
        $this->downloader = $downloader;
        $this->urlHandler = $urlHandler;
        $this->processor = $processor;
    }

    /**
     * Adds a URL to the queue of URLs to be processed.
     *
     * @param string $url A URL to add to the queue.
     * @return bool True, if the URL was added or false, if the URL has been queued before.
     */
    public function queue($url) {
        return $this->queue->add($url);
    }

    /**
     * Crawls the given URLs.
     *
     * @return UrlException[] Errors which occurred during crawling.
     */
    public function crawl() {
        $erredUrls = [];

        while ($url = $this->queue->next()) {
            // Handle URL to download
            if ($this->urlHandler !== null) {
                if (!$this->urlHandler->handle($url)) {
                    continue;
                }
            }

            // Try to download the next URL
            try {
                $asset = $this->downloader->download($url);
            } catch (DownloaderException $e) {
                $erredUrls[] = $e;
                continue;
            }

            // Try to process the asset
            if (!$this->processor->canProcess($asset)) {
                $erredUrls[] = new UnprocessableException($url);
                continue;
            }
            try {
                $this->processor->process($asset, $this->queue);
            } catch (Processor\InputOutputException $e) {
                $erredUrls[] = $e;
            }
        }

        return $erredUrls;
    }

    /**
     * @param QueueInterface $queue
     * @return $this
     */
    public function setQueue(QueueInterface $queue) {
        $this->queue = $queue;

        return $this;
    }

    /**
     * @param DownloaderInterface $downloader
     * @return $this
     */
    public function setDownloader(DownloaderInterface $downloader) {
        $this->downloader = $downloader;

        return $this;
    }

    /**
     * @param UrlHandlerInterface $urlHandler
     * @return $this
     */
    public function setUrlHandler(UrlHandlerInterface $urlHandler) {
        $this->urlHandler = $urlHandler;

        return $this;
    }

    /**
     * @param ProcessorInterface $processor
     * @return $this
     */
    public function setProcessor(ProcessorInterface $processor) {
        $this->processor = $processor;

        return $this;
    }
}
