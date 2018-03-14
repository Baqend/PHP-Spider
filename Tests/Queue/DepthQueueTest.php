<?php

namespace Baqend\Component\Spider\Tests\Queue;

use Baqend\Component\Spider\Queue\DepthQueue;
use PHPUnit\Framework\TestCase;

/**
 * Class DepthQueueTest created on 2018-03-14.
 *
 * @author  Konstantin Simon Maria Möllers
 * @package Baqend\Component\Spider\Tests
 */
class DepthQueueTest extends TestCase
{

    /**
     * @var DepthQueue
     */
    private $queue;

    protected function setUp() {
        $this->queue = new DepthQueue();
    }

    /**
     * @test
     */
    public function add() {
        // Can basically add to LIFO queue
        $this->assertTrue($this->queue->add('https://example.org/demo'));
        $this->assertTrue($this->queue->add('https://example.org/xyz'));
        $this->assertSame('https://example.org/xyz', $this->queue->next());
        $this->assertSame('https://example.org/demo', $this->queue->next());
        $this->assertNull($this->queue->next());

        // Cannot add same URLs again
        $this->assertFalse($this->queue->add('https://example.org/demo'));
        $this->assertFalse($this->queue->add('https://example.org/xyz'));
        $this->assertNull($this->queue->next());

        // Can add a different one again once
        $this->assertTrue($this->queue->add('https://example.org/abc'));
        $this->assertFalse($this->queue->add('https://example.org/abc'));
        $this->assertSame('https://example.org/abc', $this->queue->next());
        $this->assertNull($this->queue->next());
    }
}
