<?php

namespace Baqend\Component\Spider;

/**
 * Class Resource created on 2018-03-14.
 *
 * @author  Konstantin Simon Maria Möllers
 * @package Baqend\Component\Spider
 */
class Resource
{

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $contentType;

    /**
     * @var string
     */
    private $content;

    /**
     * Resource constructor.
     *
     * @param string $url The URL identifying this resource.
     * @param string $contentType This resource's content type.
     * @param string $content This resource's content.
     */
    public function __construct($url, $contentType, $content) {
        $this->url = $url;
        $this->contentType = $contentType;
        $this->content = $content;
    }

    /**
     * Creates a new resource based on this one with new content.
     *
     * @param string $newContent The new content to use.
     * @return static The cloned resource.
     */
    public function withContent($newContent) {
        return new static($this->url, $this->contentType, $newContent);
    }

    /**
     * Return the URL identifying this resource.
     *
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * Return this resource's content type.
     *
     * @return string
     */
    public function getContentType() {
        return $this->contentType;
    }

    /**
     * Return this resource's content.
     *
     * @return string
     */
    public function getContent() {
        return $this->content;
    }
}
