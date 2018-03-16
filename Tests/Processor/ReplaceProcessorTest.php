<?php

namespace Baqend\Component\Spider\Tests\Processor;

use Baqend\Component\Spider\Processor\ReplaceProcessor;
use Baqend\Component\Spider\Queue\BreadthQueue;
use Baqend\Component\Spider\Tests\DummyAsset;
use PHPUnit\Framework\TestCase;

/**
 * Class ReplaceProcessorTest created on 2018-03-15.
 *
 * @author  Konstantin Simon Maria Möllers
 * @package Baqend\Component\Spider\Tests\Processor
 */
class ReplaceProcessorTest extends TestCase
{

    /**
     * @var ReplaceProcessor
     */
    private $processor;

    /**
     * @var BreadthQueue
     */
    private $queue;

    protected function setUp() {
        $this->processor = new ReplaceProcessor('https://example.org', 'https://example.com/archive');
        $this->queue = new BreadthQueue();
    }

    /**
     * @test
     */
    public function canProcess() {
        // URL rewrite processor can always process
        $this->assertTrue($this->processor->canProcess(new DummyAsset('https://example.org/index.html')));

        // URL rewrite processor passes through if next processor can process
        $this->assertTrue($this->processor->canProcess(new DummyAsset('https://other.org/index.html')));
    }

    /**
     * @test
     */
    public function process() {
        $processor = $this->processor;
        $queue = $this->queue;

        // Basic replace
        $asset = $processor->process(new DummyAsset('https://example.org/index.html', 200, 'text/html', '<a href="https://example.org/other.html">Other</a>'), $queue);
        $this->assertEquals('https://example.org/index.html', $asset->getUrl());
        $this->assertEquals('<a href="https://example.com/archive/other.html">Other</a>', $asset->getContent());
        $this->assertNull($queue->next());
    }
}
