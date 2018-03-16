<?php

namespace Baqend\Component\Spider\Tests\Processor;

use Baqend\Component\Spider\Processor\CssProcessor;
use Baqend\Component\Spider\Processor\HtmlProcessor;
use Baqend\Component\Spider\Processor\Processor;
use PHPUnit\Framework\TestCase;

/**
 * Class ProcessorTest created on 2018-03-16.
 *
 * @author  Konstantin Simon Maria Möllers
 * @package Baqend\Component\Spider\Tests\Processor
 */
class ProcessorTest extends TestCase
{

    /**
     * @var Processor
     */
    private $processor;

    protected function setUp() {
        $this->processor = new Processor();
    }

    /**
     * @test
     */
    public function addProcessor() {
        $p1 = new CssProcessor();
        $p2 = new HtmlProcessor($p1);

        $this->assertFalse($this->processor->hasProcessor($p1));
        $this->assertFalse($this->processor->hasProcessor($p2));

        $this->processor->addProcessor($p1);
        $this->assertTrue($this->processor->hasProcessor($p1));
        $this->assertFalse($this->processor->hasProcessor($p2));

        $this->processor->addProcessor($p2);
        $this->assertTrue($this->processor->hasProcessor($p1));
        $this->assertTrue($this->processor->hasProcessor($p2));
    }

    /**
     * @test
     */
    public function removeProcessor() {
        $p1 = new CssProcessor();
        $p2 = new HtmlProcessor($p1);

        $this->assertFalse($this->processor->removeProcessor($p1));
        $this->assertFalse($this->processor->removeProcessor($p2));

        $this->processor->addProcessor($p1);
        $this->processor->addProcessor($p2);
        $this->assertTrue($this->processor->hasProcessor($p1));
        $this->assertTrue($this->processor->hasProcessor($p2));

        $this->assertTrue($this->processor->removeProcessor($p1));
        $this->assertFalse($this->processor->hasProcessor($p1));
        $this->assertTrue($this->processor->removeProcessor($p2));
        $this->assertFalse($this->processor->hasProcessor($p2));
    }
}
