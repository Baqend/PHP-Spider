<?php

namespace Baqend\Component\Spider;

use Baqend\Component\Spider\Downloader\DownloaderException;
use Baqend\Component\Spider\Downloader\DownloaderInterface;
use Baqend\Component\Spider\Processor\ProcessorInterface;
use Baqend\Component\Spider\Processor\UnprocessableException;
use Baqend\Component\Spider\Queue\QueueInterface;
use Baqend\Component\Spider\ResourceHandler\ResourceHandlerInterface;

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
     * @var ResourceHandlerInterface|null
     */
    private $resourceHandler;

    /**
     * @var ProcessorInterface|null
     */
    private $processor;

    /**
     * Spider constructor.
     *
     * @param QueueInterface $queue A queue to store URLs to download and process.
     * @param DownloaderInterface$downloader A downloader to download URLs and provide resources.
     * @param ResourceHandlerInterface|null $resourceHandler An optional handler for downloaded resources.
     * @param ProcessorInterface|null  $processor An optional processor for downloaded files.
     */
    public function __construct(
        QueueInterface $queue,
        DownloaderInterface $downloader,
        ResourceHandlerInterface $resourceHandler = null,
        ProcessorInterface $processor = null
    ) {
        $this->queue = $queue;
        $this->downloader = $downloader;
        $this->resourceHandler = $resourceHandler;
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
            // Try to download the next URL
            try {
                $resource = $this->downloader->download($url);
            } catch (DownloaderException $e) {
                $erredUrls[] = $e;
                continue;
            }

            // Handle downloaded resource
            if ($this->resourceHandler !== null) {
                $resource = $this->resourceHandler->handle($resource);
                if ($resource === null) {
                    continue;
                }
            }

            // Try to process the resource
            if (!$this->processor->canProcess($resource)) {
                $erredUrls[] = new UnprocessableException($url);
                continue;
            }
            $this->processor->process($resource, $this->queue);
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
     * @param ResourceHandlerInterface $resourceHandler
     * @return $this
     */
    public function setResourceHandler(ResourceHandlerInterface $resourceHandler) {
        $this->resourceHandler = $resourceHandler;

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
