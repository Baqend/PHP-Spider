<?php

namespace Baqend\Component\Spider\Tests;

use Baqend\Component\Spider\Asset;

/**
 * Class DummyAsset created on 2018-03-14.
 *
 * @author  Konstantin Simon Maria Möllers
 * @package Baqend\Component\Spider\Tests
 */
class DummyAsset extends Asset
{

    public function __construct($url = 'https://example.org/dummy', $statusCode = 200, $contentType = 'text/html', $content = 'dummy') {
        parent::__construct($url, $statusCode, $contentType, $content);
    }
}
