<?php

namespace Baqend\Component\Spider\Tests\Processor;

use Baqend\Component\Spider\Asset;
use Baqend\Component\Spider\Processor\CssProcessor;
use Baqend\Component\Spider\Processor\HtmlProcessor;
use Baqend\Component\Spider\Queue\BreadthQueue;
use Baqend\Component\Spider\Tests\DummyAsset;
use PHPUnit\Framework\TestCase;

class HtmlProcessorTest extends TestCase
{

    /**
     * @var BreadthQueue
     */
    private $queue;

    /**
     * @var CssProcessor
     */
    private $cssProcessor;

    /**
     * @var HtmlProcessor
     */
    private $htmlProcessor;

    protected function setUp() {
        $this->cssProcessor = new CssProcessor();
        $this->htmlProcessor = new HtmlProcessor($this->cssProcessor);
        $this->queue = new BreadthQueue();
    }

    /**
     * @test
     */
    public function canProcessTextHtml() {
        $processor = $this->htmlProcessor;

        $this->assertTrue($processor->canProcess(new DummyAsset('https://example.org/index.html', 200, 'text/html')));
        $this->assertTrue($processor->canProcess(new DummyAsset('https://example.org/index.html', 200, null)));
        $this->assertTrue($processor->canProcess(new DummyAsset('https://example.org/index.html', 200, 'text/html; charset=utf-8')));
    }

    /**
     * @test
     */
    public function cantProcessOther() {
        $processor = $this->htmlProcessor;

        $this->assertFalse($processor->canProcess(new DummyAsset('https://example.org/style.css', 200, 'text/css')));
        $this->assertFalse($processor->canProcess(new DummyAsset('https://example.org/main.js', 200, 'application/javascript')));
        $this->assertFalse($processor->canProcess(new DummyAsset('https://example.org/main.js', 200, 'application/javascript')));
        $this->assertFalse($processor->canProcess(new DummyAsset('https://example.org/style.css', 200, null)));
        $this->assertFalse($processor->canProcess(new DummyAsset('https://example.org/style.css', 200, 'text/css; charset=utf-8')));
        $this->assertFalse($processor->canProcess(new DummyAsset('https://example.org/index.svg', 200, null)));
        $this->assertFalse($processor->canProcess(new DummyAsset('https://example.org/index.svg', 200, 'text/xml+svg')));
    }

    /**
     * @test
     */
    public function processLink() {
        $processor = $this->htmlProcessor;
        $queue = $this->queue;

        $asset = $this->createAsset('/subdir/index.html', $this->htmlHead('<link href="/styles/main.css">'));
        $queue->add($asset->getUrl());
        $html = $processor->process($asset, $queue);

        $this->assertEquals($this->htmlHead('<link href="https://example.org/styles/main.css">'), $html->getContent());
        $this->assertEquals('https://example.org/subdir/index.html', $queue->next());
        $this->assertEquals('https://example.org/styles/main.css', $queue->next());
        $this->assertNull($queue->next());

        $asset = $this->createAsset('/subdir/index.html', $this->htmlHead('<link href="/styles/main.css?version=2018">'));
        $queue->add($asset->getUrl());
        $html = $processor->process($asset, $queue);

        $this->assertEquals($this->htmlHead('<link href="https://example.org/styles/main.css?version=2018">'), $html->getContent());
        $this->assertNull($queue->next());

        $asset = $this->createAsset('/subdir/index.html', $this->htmlHead('<link href="/styles/main.css#fragment">'));
        $queue->add($asset->getUrl());
        $html = $processor->process($asset, $queue);

        $this->assertEquals($this->htmlHead('<link href="https://example.org/styles/main.css#fragment">'), $html->getContent());
        $this->assertNull($queue->next());
    }

    /**
     * @test
     */
    public function processScript() {
        $processor = $this->htmlProcessor;
        $queue = $this->queue;

        $asset = $this->createAsset('/subdir/index.html', $this->htmlHead('<script src="/scripts/main.js"></script>'));
        $queue->add($asset->getUrl());
        $html = $processor->process($asset, $queue);

        $this->assertEquals($this->htmlHead('<script src="https://example.org/scripts/main.js"></script>'), $html->getContent());
        $this->assertEquals('https://example.org/subdir/index.html', $queue->next());
        $this->assertEquals('https://example.org/scripts/main.js', $queue->next());
        $this->assertNull($queue->next());
    }

    /**
     * @test
     */
    public function processAnchor() {
        $processor = $this->htmlProcessor;
        $queue = $this->queue;

        $asset = $this->createAsset('/subdir/index.html', $this->htmlBody('<a href="next/index.html">Hello World</a>'));
        $queue->add($asset->getUrl());
        $html = $processor->process($asset, $queue);

        $this->assertEquals($this->htmlBody('<a href="https://example.org/subdir/next/index.html">Hello World</a>'), $html->getContent());
        $this->assertEquals('https://example.org/subdir/index.html', $queue->next());
        $this->assertEquals('https://example.org/subdir/next/index.html', $queue->next());
        $this->assertNull($queue->next());
    }

    /**
     * @test
     */
    public function processSrcsetAttributes() {
        $processor = $this->htmlProcessor;
        $queue = $this->queue;

        $asset = $this->createAsset('/subdir/index.html', $this->htmlBody('<img srcset="/img/2x.png 2x, /img/4x.png 4x">'));
        $queue->add($asset->getUrl());
        $html = $processor->process($asset, $queue);

        $this->assertEquals($this->htmlBody('<img srcset="https://example.org/img/2x.png 2x, https://example.org/img/4x.png 4x">'), $html->getContent());
        $this->assertEquals('https://example.org/subdir/index.html', $queue->next());
        $this->assertEquals('https://example.org/img/2x.png', $queue->next());
        $this->assertEquals('https://example.org/img/4x.png', $queue->next());
        $this->assertNull($queue->next());
    }

    /**
     * @test
     */
    public function processStyleAttributes() {
        $processor = $this->htmlProcessor;
        $queue = $this->queue;

        $asset = $this->createAsset('/subdir/index.html', $this->htmlBody('<div style="background-image: url(../img/other.jpeg)"></div>'));
        $queue->add($asset->getUrl());
        $html = $processor->process($asset, $queue);

        $this->assertEquals($this->htmlBody('<div style="background-image: url(https://example.org/img/other.jpeg)"></div>'), $html->getContent());
        $this->assertEquals('https://example.org/subdir/index.html', $queue->next());
        $this->assertEquals('https://example.org/img/other.jpeg', $queue->next());
        $this->assertNull($queue->next());
    }

    /**
     * @param string $path
     * @param string $html
     * @return Asset
     */
    private function createAsset($path, $html) {
        return new DummyAsset('https://example.org'.$path, 200, 'text/html', $html);
    }

    /**
     * @param string $head
     * @return string
     */
    private function htmlHead($head) {
        return "<!DOCTYPE html>\n<html><head>$head</head><body></body></html>\n";
    }

    /**
     * @param string $body
     * @return string
     */
    private function htmlBody($body) {
        return "<!DOCTYPE html>\n<html><head></head><body>$body</body></html>\n";
    }
}
