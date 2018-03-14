<?php

namespace Baqend\Component\Spider\Tests\ResourceHandler;

use Baqend\Component\Spider\ResourceHandler\BlacklistResourceHandler;
use Baqend\Component\Spider\Tests\DummyResource;
use PHPUnit\Framework\TestCase;

/**
 * Class BlacklistResourceHandlerTest created on 2018-03-14.
 *
 * @author  Konstantin Simon Maria Möllers
 * @package Baqend\Component\Spider\Tests\ResourceHandler
 */
class BlacklistResourceHandlerTest extends TestCase
{

    /**
     * @test
     */
    public function handlesCorrectly() {
        $blacklist = [
            'https://example.org/**',
            'https://example.de/**',
        ];

        $handler = new BlacklistResourceHandler($blacklist);
        $this->assertNull($handler->handle(new DummyResource('https://example.org/dummy')));

        $handler = new BlacklistResourceHandler($blacklist);
        $this->assertNull($handler->handle(new DummyResource('https://example.de/dummy')));

        $resource = new DummyResource('https://example.com/dummy');
        $this->assertSame($resource, $handler->handle($resource));
    }
}
