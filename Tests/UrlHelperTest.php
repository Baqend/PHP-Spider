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
}
