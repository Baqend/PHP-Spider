<?php

namespace Baqend\Component\Spider\Tests;

use Baqend\Component\Spider\UrlHelper;
use PHPUnit\Framework\TestCase;

/**
 * Class UrlHelperTest created on 2018-03-14.
 *
 * @author  Konstantin Simon Maria Möllers
 * @package Baqend\Component\Spider\Tests
 */
class UrlHelperTest extends TestCase
{

    /**
     * @test
     */
    public function globToRegExp() {
        $regExp = UrlHelper::globToRegExp('https://example.com/**');
        $this->assertEquals('/^https:\/\/example\.com\/(?:[^\/]*(?:\/|$))*$/', $regExp);
        $this->assertEquals(1, preg_match($regExp, 'https://example.com/'));
        $this->assertEquals(1, preg_match($regExp, 'https://example.com/file'));
        $this->assertEquals(1, preg_match($regExp, 'https://example.com/some/file'));
        $this->assertEquals(1, preg_match($regExp, 'https://example.com/some/directory/'));
        $this->assertEquals(1, preg_match($regExp, 'https://example.com/some/file.ext'));
        $this->assertEquals(0, preg_match($regExp, 'https://example.com'));

        $regExp = UrlHelper::globToRegExp('https://example.com/*');
        $this->assertEquals('/^https:\/\/example\.com\/[^\/]*$/', $regExp);
        $this->assertEquals(1, preg_match($regExp, 'https://example.com/file'));
        $this->assertEquals(0, preg_match($regExp, 'https://example.com/some/file'));

        $this->assertEquals('/^(?:[^\/]*(?:\/|$))*$/i', UrlHelper::globToRegExp('**', true));
        $this->assertEquals('/^[^\/]*$/i', UrlHelper::globToRegExp('*', true));

        $this->assertEquals('/^http(?:|s):\/\/example\.com$/', UrlHelper::globToRegExp('http{,s}://example.com'));
        $this->assertEquals('/^http(?:|s):\/\/example\.com\/[^\/]*\/demo$/', UrlHelper::globToRegExp('http{,s}://example.com/*/demo'));
        $this->assertEquals('/^http(?:|s):\/\/example\.com\/[^\/]*\.html$/', UrlHelper::globToRegExp('http{,s}://example.com/*.html'));
        $this->assertEquals('/^http(?:|s):\/\/example\.com\/[^\/]*\.(?:html|jpeg)$/', UrlHelper::globToRegExp('http{,s}://example.com/*.{html,jpeg}'));
        $this->assertEquals('/^http(?:|s):\/\/example\.com\/[^\/]*\.(?:html|jpeg)$/', UrlHelper::globToRegExp('http{,s}://example.com/*.{html, jpeg}'));
    }

    /**
     * @test
     */
    public function resolve() {
        $this->assertEquals(
            'https://www.other.org/my/path',
            UrlHelper::resolve(
                'https://www.example.org/your/path',
                'https://www.other.org/my/path'
            )
        );

        $this->assertEquals(
            'https://www.other.org/my/path#fragment',
            UrlHelper::resolve(
                'https://www.example.org/your/path',
                'https://www.other.org/my/path#fragment'
            )
        );

        $this->assertEquals(
            'https://www.other.org/my/path',
            UrlHelper::resolve(
                'https://www.example.org/your/path',
                '//www.other.org/my/path'
            )
        );

        $this->assertEquals(
            'https://www.example.org/my/path',
            UrlHelper::resolve(
                'https://www.example.org/your/path',
                '/my/path'
            )
        );

        $this->assertEquals(
            'https://www.example.org/my/path#fragment',
            UrlHelper::resolve(
                'https://www.example.org/your/path',
                '/my/path#fragment'
            )
        );

        $this->assertEquals(
            'https://www.example.org/your/index.html',
            UrlHelper::resolve(
                'https://www.example.org/your/path',
                'index.html'
            )
        );

        $this->assertEquals(
            'https://www.example.org/index.html',
            UrlHelper::resolve(
                'https://www.example.org/your/path',
                '../index.html'
            )
        );

        $this->assertEquals(
            'https://www.example.org/your/index.html',
            UrlHelper::resolve(
                'https://www.example.org/your/path/',
                '../index.html'
            )
        );

        $this->assertEquals(
            'https://www.example.org/your/directory/',
            UrlHelper::resolve(
                'https://www.example.org/your/path/',
                '../directory/'
            )
        );

        $this->assertEquals(
            'https://www.example.org/your/path/index.html',
            UrlHelper::resolve(
                'https://www.example.org/your/path/',
                'index.html'
            )
        );

        $this->assertEquals(
            'https://www.example.org/your/path/some/deeper/directory/index.html',
            UrlHelper::resolve(
                'https://www.example.org/your/path/',
                'some/deeper/directory/index.html'
            )
        );

        $this->assertEquals(
            'https://www.example.org/your/path/some/deeper/directory/',
            UrlHelper::resolve(
                'https://www.example.org/your/path/',
                'some/deeper/directory/'
            )
        );

        $this->assertEquals(
            'https://www.example.org/your/path/index.html',
            UrlHelper::resolve(
                'https://www.example.org/your/path/',
                './././index.html'
            )
        );

        $this->assertEquals(
            'https://www.example.org/your/path/',
            UrlHelper::resolve(
                'https://www.example.org/your/path/',
                '././.'
            )
        );

        $this->assertEquals(
            'https://www.example.org/your/path',
            UrlHelper::resolve(
                'https://www.example.org/your/path',
                '././.'
            )
        );

        $this->assertEquals(
            'https://www.example.org/your/path',
            UrlHelper::resolve(
                'https://www.example.org/your/path',
                '.'
            )
        );

        $this->assertEquals(
            'https://www.example.org/your/path/',
            UrlHelper::resolve(
                'https://www.example.org/your/path',
                './././'
            )
        );

        $this->assertEquals(
            'https://www.example.org/index.html',
            UrlHelper::resolve(
                'https://www.example.org',
                'index.html'
            )
        );

        $this->assertNull(
            UrlHelper::resolve(
                'https://www.example.org',
                '../index.html'
            )
        );

        $this->assertEquals(
            'https://www.example.org/your/path',
            UrlHelper::resolve(
                'https://www.example.org/your/path',
                ''
            )
        );

        $this->assertEquals(
            'https://www.example.org/your/path#fragment',
            UrlHelper::resolve(
                'https://www.example.org/your/path',
                '#fragment'
            )
        );
    }

    /**
     * @test
     */
    public function extractSchema() {
        $this->assertEquals('https', UrlHelper::extractSchema('https://example.org'));
        $this->assertEquals('http', UrlHelper::extractSchema('http://example.org'));
        $this->assertNull(UrlHelper::extractSchema('ftp://example.org'));
        $this->assertNull(UrlHelper::extractSchema('//example.org'));
        $this->assertNull(UrlHelper::extractSchema('/some/absolute/path'));
        $this->assertNull(UrlHelper::extractSchema('some/relative/path'));
    }

    /**
     * @test
     */
    public function extractOrigin() {
        $this->assertEquals('https://example.org', UrlHelper::extractOrigin('https://example.org'));
        $this->assertEquals('http://example.org', UrlHelper::extractOrigin('http://example.org'));
        $this->assertEquals('https://example.org', UrlHelper::extractOrigin('https://example.org/'));
        $this->assertEquals('http://example.org', UrlHelper::extractOrigin('http://example.org/'));
        $this->assertEquals('https://example.org', UrlHelper::extractOrigin('https://example.org/some/path'));
        $this->assertEquals('http://example.org', UrlHelper::extractOrigin('http://example.org/some/path'));
        $this->assertNull(UrlHelper::extractOrigin('ftp://example.org'));
        $this->assertNull(UrlHelper::extractOrigin('//example.org'));
        $this->assertNull(UrlHelper::extractOrigin('/some/absolute/path'));
        $this->assertNull(UrlHelper::extractOrigin('some/relative/path'));
    }

    /**
     * @test
     */
    public function extractPath() {
        $this->assertEquals('', UrlHelper::extractPath('https://example.org'));
        $this->assertEquals('', UrlHelper::extractPath('http://example.org'));
        $this->assertEquals('/', UrlHelper::extractPath('https://example.org/'));
        $this->assertEquals('/', UrlHelper::extractPath('http://example.org/'));
        $this->assertEquals('/some/path', UrlHelper::extractPath('https://example.org/some/path'));
        $this->assertEquals('/some/path', UrlHelper::extractPath('http://example.org/some/path'));
        $this->assertNull(UrlHelper::extractPath('ftp://example.org'));
        $this->assertNull(UrlHelper::extractPath('//example.org'));
        $this->assertNull(UrlHelper::extractPath('/some/absolute/path'));
        $this->assertNull(UrlHelper::extractPath('some/relative/path'));
    }

    /**
     * @test
     */
    public function extractOriginAndPath() {
        $this->assertEquals(['https://example.org', ''], UrlHelper::extractOriginAndPath('https://example.org'));
        $this->assertEquals(['http://example.org', ''], UrlHelper::extractOriginAndPath('http://example.org'));
        $this->assertEquals(['https://example.org', '/'], UrlHelper::extractOriginAndPath('https://example.org/'));
        $this->assertEquals(['http://example.org', '/'], UrlHelper::extractOriginAndPath('http://example.org/'));
        $this->assertEquals(['https://example.org', '/some/path'], UrlHelper::extractOriginAndPath('https://example.org/some/path'));
        $this->assertEquals(['http://example.org', '/some/path'], UrlHelper::extractOriginAndPath('http://example.org/some/path'));
        $this->assertEquals([null, null], UrlHelper::extractOriginAndPath('ftp://example.org'));
        $this->assertEquals([null, null], UrlHelper::extractOriginAndPath('//example.org'));
        $this->assertEquals([null, null], UrlHelper::extractOriginAndPath('/some/absolute/path'));
        $this->assertEquals([null, null], UrlHelper::extractOriginAndPath('some/relative/path'));
    }

    /**
     * @test
     */
    public function stripQueryFragment() {
        $this->assertEquals('https://example.org', UrlHelper::stripQueryFragment('https://example.org#demo'));
        $this->assertEquals('https://example.org/', UrlHelper::stripQueryFragment('https://example.org/#demo'));
        $this->assertEquals('https://example.org/test', UrlHelper::stripQueryFragment('https://example.org/test#demo'));

        $this->assertEquals('https://example.org', UrlHelper::stripQueryFragment('https://example.org?demo'));
        $this->assertEquals('https://example.org/', UrlHelper::stripQueryFragment('https://example.org/?demo'));
        $this->assertEquals('https://example.org/test', UrlHelper::stripQueryFragment('https://example.org/test?demo'));

        $this->assertEquals('https://example.org', UrlHelper::stripQueryFragment('https://example.org?demo#foobar'));
        $this->assertEquals('https://example.org/', UrlHelper::stripQueryFragment('https://example.org/?demo#foobar'));
        $this->assertEquals('https://example.org/test', UrlHelper::stripQueryFragment('https://example.org/test?demo#foobar'));
    }
}
