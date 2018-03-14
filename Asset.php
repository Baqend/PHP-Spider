<?php

namespace Baqend\Component\Spider;

/**
 * Class Asset created on 2018-03-14.
 *
 * @author  Konstantin Simon Maria Möllers
 * @package Baqend\Component\Spider
 */
class Asset
{

    private $url;
    private $statusCode;
    private $contentType;
    private $content;

    /**
     * Asset constructor.
     *
     * @param string $url The URL identifying this asset.
     * @param int $statusCode The URL response's status code.
     * @param string $contentType This asset's content type.
     * @param string $content This asset's content.
     */
    public function __construct($url, $statusCode, $contentType, $content) {
        $this->url = $url;
        $this->statusCode = $statusCode;
        $this->contentType = $contentType;
        $this->content = $content;
    }

    /**
     * Creates a new asset based on this one with new content.
     *
     * @param string $newContent The new content to use.
     * @return static The cloned asset.
     */
    public function withContent($newContent) {
        return new static($this->url, $this->statusCode, $this->contentType, $newContent);
    }

    /**
     * Return the URL identifying this asset.
     *
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * @return int
     */
    public function getStatusCode() {
        return $this->statusCode;
    }

    /**
     * Return this asset's content type.
     *
     * @return string
     */
    public function getContentType() {
        return $this->contentType;
    }

    /**
     * Return this asset's content.
     *
     * @return string
     */
    public function getContent() {
        return $this->content;
    }
}
