<?php

namespace Baqend\Component\Spider\Tests\UrlHandler;

use Baqend\Component\Spider\UrlHandler\BlacklistUrlHandler;
use PHPUnit\Framework\TestCase;

/**
 * Class BlacklistUrlHandlerTest created on 2018-03-14.
 *
 * @author  Konstantin Simon Maria Möllers
 * @package Baqend\Component\Spider\Tests\UrlHandler
 */
class BlacklistUrlHandlerTest extends TestCase
{

    /**
     * @test
     */
    public function handlesCorrectly() {
        $blacklist = [
            'https://example.org/**',
            'https://example.de/**',
        ];

        $handler = new BlacklistUrlHandler($blacklist);

        $this->assertFalse($handler->handle('https://example.org/dummy'));
        $this->assertFalse($handler->handle('https://example.de/dummy'));
        $this->assertTrue($handler->handle('https://example.com/dummy'));
        $this->assertFalse($handler->handle('https://example.de/dummy?query'));
        $this->assertTrue($handler->handle('https://example.com/dummy?query'));
        $this->assertFalse($handler->handle('https://example.de/dummy#fragment'));
        $this->assertTrue($handler->handle('https://example.com/dummy#fragment'));
    }
}
