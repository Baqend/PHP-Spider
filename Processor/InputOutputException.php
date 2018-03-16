<?php

namespace Baqend\Component\Spider\Processor;

use Baqend\Component\Spider\UrlException;

/**
 * Class InputOutputException created on 2018-03-16.
 *
 * @author  Konstantin Simon Maria Möllers
 * @package Baqend\Component\Spider\Processor
 */
class InputOutputException extends UrlException
{

    /**
     * InputOutputException constructor.
     *
     * @param string $url
     * @param string $filename
     * @param boolean $readOrWrite
     * @param string $reason
     */
    public function __construct($url, $filename, $readOrWrite, $reason = '') {
        $message = 'Cannot '.($readOrWrite ? 'read from' : 'write to').' '.$filename.($reason ? ': '.$reason : '');

        parent::__construct($url, $message);
    }
}
