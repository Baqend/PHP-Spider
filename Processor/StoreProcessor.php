<?php

namespace Baqend\Component\Spider\Processor;

use Baqend\Component\Spider\Asset;
use Baqend\Component\Spider\Queue\QueueInterface;

/**
 * This processor is able to store given assets.
 *
 * If a given prefix is matched against an asset, its sub-URL will be
 * added to a root directory, where it is then stored to disk.
 *
 * Class StoreProcessor created on 2018-03-16.
 *
 * @author  Konstantin Simon Maria Möllers
 * @package Baqend\Component\Spider\Processor
 */
class StoreProcessor implements ProcessorInterface
{

    private $prefix;
    private $rootDirectory;

    /**
     * StoreProcessor constructor.
     *
     * @param string $prefix A URL prefix to cut off when storing an asset.
     * @param string $rootDirectory The root directory which will be mapped to the $prefix.
     */
    public function __construct($prefix, $rootDirectory) {
        $this->prefix = $prefix;
        $this->rootDirectory = $rootDirectory;
    }

    /**
     * Checks whether this asset can be processed.
     *
     * @param Asset $asset
     * @return boolean True, if the given asset can be processed by this processor.
     */
    public function canProcess(Asset $asset) {
        return strpos($asset->getUrl(), $this->prefix) === 0;
    }

    /**
     * Processes a given asset.
     *
     * Discovered new assets are written to the provided queue.
     *
     * @param Asset $asset The asset to be processed.
     * @param QueueInterface $queue The queue to add new files to.
     * @return Asset The processed asset.
     * @throws InputOutputException When I/O operations during processing fail.
     */
    public function process(Asset $asset, QueueInterface $queue) {
        $this->storeAsset($asset);

        return $asset;
    }

    /**
     * Stores the asset to disk, if prefix is matched.
     *
     * @param Asset $asset The asset to store.
     * @throws InputOutputException When the file could not be stored.
     */
    public function storeAsset(Asset $asset) {
        // Check if prefix is matched
        $url = $asset->getUrl();
        if (strpos($url, $this->prefix) !== 0) {
            return;
        }

        $filename = $this->rootDirectory . substr($url, strlen($this->prefix));

        // Ensure directory exists
        $directory = dirname($filename);
        if (!is_dir($directory)) {
            if (!mkdir($directory, 0777, true)) {
                throw new InputOutputException($url, $filename, false, 'Could not make parent dir.');
            }
        }

        // Try to put file contents
        if (file_put_contents($filename, $asset->getContent()) === false) {
            throw new InputOutputException($url, $filename, false, 'File is not writable.');
        }
    }
}
