<?php

namespace Baqend\Component\Spider\Tests\Processor;

use Baqend\Component\Spider\Processor\UrlRewriteProcessor;
use Baqend\Component\Spider\Queue\BreadthQueue;
use Baqend\Component\Spider\Tests\DummyAsset;
use PHPUnit\Framework\TestCase;

/**
 * Class UrlRewriteProcessorTest created on 2018-03-15.
 *
 * @author  Konstantin Simon Maria Möllers
 * @package Baqend\Component\Spider\Tests\Processor
 */
class UrlRewriteProcessorTest extends TestCase
{

    /**
     * @var UrlRewriteProcessor
     */
    private $processor;

    /**
     * @var BreadthQueue
     */
    private $queue;

    protected function setUp() {
        $this->processor = new UrlRewriteProcessor('https://example.org', 'https://example.com/archive');
        $this->queue = new BreadthQueue();
    }

    /**
     * @test
     */
    public function canProcess() {
        // URL rewrite processor can always process
        $this->assertTrue($this->processor->canProcess(new DummyAsset('https://example.org/index.html')));

        // URL rewrite processor passes through if next processor can process
        $this->assertFalse($this->processor->canProcess(new DummyAsset('https://other.org/index.html')));
    }

    /**
     * @test
     */
    public function process() {
        $processor = $this->processor;
        $queue = $this->queue;

        // Basic rewrite
        $asset = $processor->process(new DummyAsset('https://example.org/index.html'), $queue);
        $this->assertEquals('https://example.com/archive/index.html', $asset->getUrl());
        $this->assertNull($queue->next());

        // Rewrites without path
        $asset = $processor->process(new DummyAsset('https://example.org'), $queue);
        $this->assertEquals('https://example.com/archive', $asset->getUrl());
        $this->assertNull($queue->next());

        // Does not rewrite other URL
        $asset = $processor->process(new DummyAsset('https://other.org/index.html'), $queue);
        $this->assertEquals('https://other.org/index.html', $asset->getUrl());
        $this->assertNull($queue->next());

        // Does not rewrite middle of URL
        $asset = $processor->process(new DummyAsset('https://other.org/https://example.org/index.html'), $queue);
        $this->assertEquals('https://other.org/https://example.org/index.html', $asset->getUrl());
        $this->assertNull($queue->next());
    }
}
