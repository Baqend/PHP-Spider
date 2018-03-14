<?php

namespace Baqend\Component\Spider;

/**
 * Class UrlException created on 2018-03-14.
 *
 * @author  Konstantin Simon Maria Möllers
 * @package Baqend\Component\Spider
 */
class UrlException extends \Exception
{

    /**
     * @var string
     */
    private $url;

    /**
     * UrlException constructor.
     *
     * @param string $url The URL which caused this exception.
     * @param string $message The Exception message to throw.
     * @param int $code The Exception code.
     * @param \Exception|null $previous The previous throwable used for the exception chaining.
     */
    public function __construct($url, $message = '', $code = 0, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
        $this->url = $url;
    }

    /**
     * The URL which caused this exception.
     *
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }
}
