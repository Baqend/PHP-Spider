<?php

namespace Baqend\Component\Spider\Tests;

use Baqend\Component\Spider\Resource;

/**
 * Class DummyResource created on 2018-03-14.
 *
 * @author  Konstantin Simon Maria Möllers
 * @package Baqend\Component\Spider\Tests
 */
class DummyResource extends Resource
{

    public function __construct($url = 'https://example.org/dummy', $contentType = 'text/html', $content = 'dummy') {
        parent::__construct($url, $contentType, $content);
    }
}
