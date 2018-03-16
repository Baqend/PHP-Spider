<?php

namespace Baqend\Component\Spider\Tests\UrlHandler;

use Baqend\Component\Spider\UrlHandler\OriginUrlHandler;
use PHPUnit\Framework\TestCase;

/**
 * Class OriginUrlHandlerTest created on 2018-03-16.
 *
 * @author  Konstantin Simon Maria Möllers
 * @package Baqend\Component\Spider\Tests\UrlHandler
 */
class OriginUrlHandlerTest extends TestCase
{

    /**
     * @test
     */
    public function handlesCorrectly() {
        $handler = new OriginUrlHandler('https://example.org');

        $this->assertTrue($handler->handle('https://example.org/dummy'));
        $this->assertFalse($handler->handle('https://example.de/dummy'));
        $this->assertFalse($handler->handle('https://example.com/dummy'));
    }
}
