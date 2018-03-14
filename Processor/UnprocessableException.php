<?php

namespace Baqend\Component\Spider\Processor;

use Baqend\Component\Spider\UrlException;

/**
 * Class UnprocessableException created on 2018-03-14.
 *
 * @author  Konstantin Simon Maria Möllers
 * @package Baqend\Component\Spider\Processor
 */
class UnprocessableException extends UrlException
{

    /**
     * UnprocessableException constructor.
     *
     * @param string $url The URL which caused this exception.
     */
    public function __construct($url) {
        $message = "Cannot process $url";
        parent::__construct($url, $message);
    }
}
