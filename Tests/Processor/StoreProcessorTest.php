<?php

namespace Baqend\Component\Spider\Tests\Processor;

use Baqend\Component\Spider\Processor\InputOutputException;
use Baqend\Component\Spider\Processor\StoreProcessor;
use Baqend\Component\Spider\Queue\BreadthQueue;
use Baqend\Component\Spider\Tests\DummyAsset;
use PHPUnit\Framework\TestCase;

/**
 * Class StoreProcessorTest created on 2018-03-16.
 *
 * @author  Konstantin Simon Maria Möllers
 * @package Baqend\Component\Spider\Tests\Processor
 */
class StoreProcessorTest extends TestCase
{

    /**
     * @var StoreProcessor
     */
    private $processor;

    /**
     * @var BreadthQueue
     */
    private $queue;

    protected function setUp() {
        $this->processor = new StoreProcessor('https://example.org', '/tmp');
        $this->queue = new BreadthQueue();
    }

    /**
     * @test
     */
    public function canProcess() {
        // URL rewrite processor can always process
        $this->assertTrue($this->processor->canProcess(new DummyAsset('https://example.org/index.html')));

        // URL rewrite processor passes through if next processor can process
        $this->assertFalse($this->processor->canProcess(new DummyAsset('https://other.com/subdirectory/index.html')));
    }

    /**
     * @test
     * @throws InputOutputException
     */
    public function process() {
        $processor = $this->processor;
        $queue = $this->queue;

        // Basic store
        $asset = $processor->process(new DummyAsset('https://example.org/index.html'), $queue);
        $this->assertEquals('https://example.org/index.html', $asset->getUrl());
        $this->assertFileExists('/tmp/index.html');
        $this->assertEquals($asset->getContent(), file_get_contents('/tmp/index.html'));
        $this->assertNull($queue->next());
        $this->assertTrue(unlink('/tmp/index.html'));

        // Storing nested assets
        $asset = $processor->process(new DummyAsset('https://example.org/subdirectory/index.html'), $queue);
        $this->assertEquals('https://example.org/subdirectory/index.html', $asset->getUrl());
        $this->assertFileExists('/tmp/subdirectory/index.html');
        $this->assertEquals($asset->getContent(), file_get_contents('/tmp/subdirectory/index.html'));
        $this->assertNull($queue->next());
        $this->assertTrue(unlink('/tmp/subdirectory/index.html'));
        $this->assertTrue(rmdir('/tmp/subdirectory'));

        // Not storing other URLs
        $asset = $processor->process(new DummyAsset('https://other.com/subdirectory/index.html'), $queue);
        $this->assertEquals('https://other.com/subdirectory/index.html', $asset->getUrl());
        $this->assertFileNotExists('/tmp/subdirectory/index.html');
        $this->assertNull($queue->next());
    }

    /**
     * @test
     * @throws InputOutputException
     * @expectedException \Baqend\Component\Spider\Processor\InputOutputException
     * @expectedExceptionMessage Cannot write to /tmp/main.css: File is not writable.
     * @expectedExceptionCode 0
     */
    public function processFailsForUnwritableFile() {
        $processor = $this->processor;
        $queue = $this->queue;

        $this->assertTrue(touch('/tmp/main.css'));
        try {
            $this->assertTrue(chmod('/tmp/main.css', 0400));
            $this->assertFalse(is_writable('/tmp/main.css'));

            // Throws exception
            $processor->process(new DummyAsset('https://example.org/main.css'), $queue);
        } finally {
            $this->assertTrue(unlink('/tmp/main.css'));
        }
    }

    /**
     * @test
     * @throws InputOutputException
     * @expectedException \Baqend\Component\Spider\Processor\InputOutputException
     * @expectedExceptionMessage Cannot write to /root/spider/subdirectory/index.html: Could not make parent dir.
     * @expectedExceptionCode 0
     */
    public function processFailsForUnwritableDirectory() {
        $processor = new StoreProcessor('https://example.org', '/root/spider');
        $queue = $this->queue;

        // Throws exception
        $processor->process(new DummyAsset('https://example.org/subdirectory/index.html'), $queue);
    }
}
