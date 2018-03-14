<?php

namespace Baqend\Component\Spider\Tests\AssetHandler;

use Baqend\Component\Spider\AssetHandler\BlacklistAssetHandler;
use Baqend\Component\Spider\Tests\DummyAsset;
use PHPUnit\Framework\TestCase;

/**
 * Class BlacklistAssetHandlerTest created on 2018-03-14.
 *
 * @author  Konstantin Simon Maria Möllers
 * @package Baqend\Component\Spider\Tests\AssetHandler
 */
class BlacklistAssetHandlerTest extends TestCase
{

    /**
     * @test
     */
    public function handlesCorrectly() {
        $blacklist = [
            'https://example.org/**',
            'https://example.de/**',
        ];

        $handler = new BlacklistAssetHandler($blacklist);
        $this->assertNull($handler->handle(new DummyAsset('https://example.org/dummy')));

        $handler = new BlacklistAssetHandler($blacklist);
        $this->assertNull($handler->handle(new DummyAsset('https://example.de/dummy')));

        $asset = new DummyAsset('https://example.com/dummy');
        $this->assertSame($asset, $handler->handle($asset));
    }
}
