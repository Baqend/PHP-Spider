<?php

namespace Baqend\Component\Spider\Tests\Processor;

use Baqend\Component\Spider\Asset;
use Baqend\Component\Spider\Processor\CssProcessor;
use Baqend\Component\Spider\Queue\BreadthQueue;
use Baqend\Component\Spider\Tests\DummyAsset;
use PHPUnit\Framework\TestCase;

class CssProcessorTest extends TestCase
{

    /**
     * @var BreadthQueue
     */
    private $queue;

    /**
     * @var CssProcessor
     */
    private $cssProcessor;

    protected function setUp() {
        $this->cssProcessor = new CssProcessor();
        $this->queue = new BreadthQueue();
    }

    /**
     * @test
     */
    public function canProcessTextCss() {
        $processor = $this->cssProcessor;

        $this->assertTrue($processor->canProcess(new DummyAsset('https://example.org/main.css', 200, 'text/css')));
        $this->assertTrue($processor->canProcess(new DummyAsset('https://example.org/main.css', 200, null)));
        $this->assertTrue($processor->canProcess(new DummyAsset('https://example.org/main.css', 200, 'text/css; charset=utf-8')));
    }

    /**
     * @test
     */
    public function cantProcessOther() {
        $processor = $this->cssProcessor;

        $this->assertFalse($processor->canProcess(new DummyAsset('https://example.org/index.html', 200, 'text/html')));
        $this->assertFalse($processor->canProcess(new DummyAsset('https://example.org/main.js', 200, 'application/javascript')));
        $this->assertFalse($processor->canProcess(new DummyAsset('https://example.org/main.js', 200, 'application/javascript')));
        $this->assertFalse($processor->canProcess(new DummyAsset('https://example.org/index.html', 200, null)));
        $this->assertFalse($processor->canProcess(new DummyAsset('https://example.org/index.html', 200, 'text/html; charset=utf-8')));
        $this->assertFalse($processor->canProcess(new DummyAsset('https://example.org/index.svg', 200, null)));
        $this->assertFalse($processor->canProcess(new DummyAsset('https://example.org/index.svg', 200, 'text/xml+svg')));
    }

    /**
     * @test
     */
    public function processUrlWithoutQuotes() {
        $processor = $this->cssProcessor;
        $queue = $this->queue;

        $asset = $this->createAsset('/style.css', 'body { background: url(img/kittens.gif); }');
        $queue->add($asset->getUrl());
        $css = $processor->process($asset, $queue);

        $this->assertEquals('body { background: url(https://example.org/img/kittens.gif); }', $css->getContent());
        $this->assertEquals('https://example.org/style.css', $queue->next());
        $this->assertEquals('https://example.org/img/kittens.gif', $queue->next());
        $this->assertNull($queue->next());
    }

    /**
     * @test
     */
    public function processUrlSingleQuotes() {
        $processor = $this->cssProcessor;
        $queue = $this->queue;

        $asset = $this->createAsset('/style.css', 'body { background: url(\'img/kittens.gif\'); }');
        $queue->add($asset->getUrl());
        $css = $processor->process($asset, $queue);

        $this->assertEquals('body { background: url(\'https://example.org/img/kittens.gif\'); }', $css->getContent());
        $this->assertEquals('https://example.org/style.css', $queue->next());
        $this->assertEquals('https://example.org/img/kittens.gif', $queue->next());
        $this->assertNull($queue->next());
    }

    /**
     * @test
     */
    public function processUrlDoubleQuotes() {
        $processor = $this->cssProcessor;
        $queue = $this->queue;

        $asset = $this->createAsset('/style.css', 'body { background: url("images/puppies.png"); }');
        $queue->add($asset->getUrl());
        $css = $processor->process($asset, $queue);

        $this->assertEquals('body { background: url("https://example.org/images/puppies.png"); }', $css->getContent());
        $this->assertEquals('https://example.org/style.css', $queue->next());
        $this->assertEquals('https://example.org/images/puppies.png', $queue->next());
        $this->assertNull($queue->next());
    }

    /**
     * @test
     */
    public function processImport() {
        $processor = $this->cssProcessor;
        $queue = $this->queue;

        // With double quotes
        $asset = $this->createAsset('/style.css', '@import "bootstrap/bootstrap.css"; body { background: red }');
        $queue->add($asset->getUrl());
        $css = $processor->process($asset, $queue);

        $this->assertEquals('@import "https://example.org/bootstrap/bootstrap.css"; body { background: red }', $css->getContent());
        $this->assertEquals('https://example.org/style.css', $queue->next());
        $this->assertEquals('https://example.org/bootstrap/bootstrap.css', $queue->next());
        $this->assertNull($queue->next());

        // With single quotes
        $asset = $this->createAsset('/style.css', '@import \'theme/main.css\'; body { background: red }');
        $queue->add($asset->getUrl());
        $css = $processor->process($asset, $queue);

        $this->assertEquals('@import \'https://example.org/theme/main.css\'; body { background: red }', $css->getContent());
        $this->assertEquals('https://example.org/theme/main.css', $queue->next());
        $this->assertNull($queue->next());

        // With url(...)
        $asset = $this->createAsset('/style.css', '@import url("assets/fonts.css"); body { background: red }');
        $css = $processor->process($asset, $queue);

        $this->assertEquals('@import url("https://example.org/assets/fonts.css"); body { background: red }', $css->getContent());
        $this->assertEquals('https://example.org/assets/fonts.css', $queue->next());
        $this->assertNull($queue->next());
    }

    /**
     * @test
     */
    public function processSelfImportWithoutEnqueueing() {
        $processor = $this->cssProcessor;
        $queue = $this->queue;

        // With double quotes
        $asset = $this->createAsset('/style.css', '@import "style.css"; body { background: red }');
        $queue->add($asset->getUrl());
        $css = $processor->process($asset, $queue);

        $this->assertEquals('@import "https://example.org/style.css"; body { background: red }', $css->getContent());
        $this->assertEquals('https://example.org/style.css', $queue->next());
        $this->assertNull($queue->next());

        // With single quotes
        $asset = $this->createAsset('/style.css', '@import \'style.css\'; body { background: red }');
        $queue->add($asset->getUrl());
        $css = $processor->process($asset, $queue);

        $this->assertEquals('@import \'https://example.org/style.css\'; body { background: red }', $css->getContent());
        $this->assertNull($queue->next());

        // With url(...)
        $asset = $this->createAsset('/style.css', '@import url("style.css"); body { background: red }');
        $css = $processor->process($asset, $queue);

        $this->assertEquals('@import url("https://example.org/style.css"); body { background: red }', $css->getContent());
        $this->assertNull($queue->next());
    }

    /**
     * @param string $path
     * @param string $css
     * @return Asset
     */
    private function createAsset($path, $css) {
        return new DummyAsset('https://example.org'.$path, 200, 'text/css', $css);
    }
}
