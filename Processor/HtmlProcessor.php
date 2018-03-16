<?php

namespace Baqend\Component\Spider\Processor;

use Baqend\Component\Spider\Asset;
use Baqend\Component\Spider\Queue\QueueInterface;
use Baqend\Component\Spider\UrlHelper;
use Symfony\Component\DomCrawler\Crawler;

/**
 * This processor finds URLs in HTML, makes them absolute, and adds
 * them to the processing queue.
 *
 * Class HtmlProcessor created on 2018-03-14.
 *
 * @author  Konstantin Simon Maria Möllers
 * @package Baqend\Component\Spider\Processor
 */
class HtmlProcessor implements ProcessorInterface
{

    private static $matchTags = [
        'a'            => ['href', 'urn'],
        'amp-img'      => ['src', 'srcset'],
        'base'         => ['href'],
        'form'         => ['action', 'data'],
        'img'          => ['src', 'usemap', 'longdesc', 'dynsrc', 'lowsrc', 'srcset'],
        'link'         => ['href'],

        'applet'       => ['code', 'codebase', 'archive', 'object'],
        'area'         => ['href'],
        'body'         => ['background', 'credits', 'instructions', 'logo'],
        'input'        => ['src', 'usemap', 'dynsrc', 'lowsrc', 'action', 'formaction'],

        'blockquote'   => ['cite'],
        'del'          => ['cite'],
        'frame'        => ['longdesc', 'src'],
        'head'         => ['profile'],
        'iframe'       => ['longdesc', 'src'],
        'ins'          => ['cite'],
        'object'       => ['archive', 'classid', 'codebase', 'data', 'usemap'],
        'q'            => ['cite'],
        'script'       => ['src'],

        'audio'        => ['src'],
        'command'      => ['icon'],
        'embed'        => ['src', 'code', 'pluginspage'],
        'event-source' => ['src'],
        'html'         => ['manifest', 'background', 'xmlns'],
        'source'       => ['src'],
        'video'        => ['src', 'poster'],

        'bgsound'      => ['src'],
        'div'          => ['href', 'src'],
        'ilayer'       => ['src'],
        'layer'        => ['src'],
        'table'        => ['background'],
        'td'           => ['background'],
        'th'           => ['background'],
        'xml'          => ['src'],

        'button'       => ['action', 'formaction'],
        'datalist'     => ['data'],
        'select'       => ['data'],

        'access'       => ['path'],
        'card'         => ['onenterforward', 'onenterbackward', 'ontimer'],
        'go'           => ['href'],
        'option'       => ['onpick'],
        'template'     => ['onenterforward', 'onenterbackward', 'ontimer'],
        'wml'          => ['xmlns'],
    ];

    /**
     * @var CssProcessor|null
     */
    private $cssProcessor;

    /**
     * HtmlProcessor constructor.
     *
     * @param CssProcessor|null $cssProcessor
     */
    public function __construct(CssProcessor $cssProcessor = null) {
        $this->cssProcessor = $cssProcessor;
    }

    /**
     * Checks whether this asset can be processed.
     *
     * @param Asset $asset
     * @return boolean True, if the given asset can be processed by this processor.
     */
    public function canProcess(Asset $asset) {
        return strpos($asset->getContentType(), 'html') !== false || substr($asset->getUrl(), -5) === '.html';
    }

    /**
     * Processes a given asset.
     *
     * Discovered new assets are written to the provided queue.
     *
     * @param Asset $asset The asset to be processed.
     * @param QueueInterface $queue The queue to add new files to.
     * @return Asset The processed asset.
     */
    public function process(Asset $asset, QueueInterface $queue) {
        // Create DOM of asset's HTML
        $domDocument = new \DOMDocument();
        if (!$domDocument->loadHTML($asset->getContent())) {
            return $asset;
        }

        // Find URLs in HTML document
        foreach (self::$matchTags as $tag => $attributes) {
            $elements = $domDocument->getElementsByTagName($tag);

            foreach ($elements as $element) {
                $this->processElement($asset, $element, $attributes, $queue);
            }
        }

        // Find URLs in CSS style-tags
        if ($this->cssProcessor !== null) {
            // Handle 'style' tags
            $crawler = new Crawler($domDocument);
            $elements = $crawler->filterXPath('//*[@style]');
            foreach ($elements as $element) {
                $cssString = $element->getAttribute('style');
                $cssString = $this->cssProcessor->processCss($asset, $cssString, $queue);

                $element->setAttribute('style', $cssString);
            }
        }

        return $asset->withContent($domDocument->saveHTML());
    }

    /**
     * Processes URLs of an HTML element.
     *
     * @param Asset $asset The asset to be processed.
     * @param \DOMElement $element The HTML element to be processed.
     * @param string[] $attributes An array of attribute names to check.
     * @param QueueInterface $queue The queue to add new files to.
     */
    private function processElement(Asset $asset, \DOMElement $element, $attributes, QueueInterface $queue) {
        foreach ($attributes as $attribute) {
            if ($element->hasAttribute($attribute)) {
                $this->processElementAttribute($asset, $element, $attribute, $queue);
            }
        }
    }

    /**
     * Processes URLs of an HTML element attribute.
     *
     * @param Asset $asset The asset to be processed.
     * @param \DOMElement $element The HTML element to be processed.
     * @param string $attribute Name of the attribute to check.
     * @param QueueInterface $queue The queue to add new files to.
     */
    private function processElementAttribute(Asset $asset, \DOMElement $element, $attribute, QueueInterface $queue) {
        if ($attribute === 'srcset') {
            $this->processElementSrcSet($asset, $element, $queue);
            return;
        }

        $currentValue = $element->getAttribute($attribute);
        $updatedValue = UrlHelper::resolve($asset->getUrl(), $currentValue);
        $queue->add(UrlHelper::stripQueryFragment($updatedValue));

        $element->setAttribute($attribute, $updatedValue);
    }

    /**
     * Processes the "srcset" attribute of an HTML element.
     *
     * @param Asset $asset The HTML document's asset.
     * @param \DOMElement $element The element being processed.
     * @param QueueInterface $queue The queue to add URLs to process to.
     */
    private function processElementSrcSet(Asset $asset, \DOMElement $element, QueueInterface $queue) {
        $value = $element->getAttribute('srcset');

        $srcset = [];
        preg_match_all('/([^"\'\s,]+)\s*\s+(\d+[wx])(?:,\s*)?/', $value, $sources, PREG_SET_ORDER);
        foreach ($sources as $source) {
            list(, $url, $config) = $source;
            $url = UrlHelper::resolve($asset->getUrl(), $url);
            $queue->add(UrlHelper::stripQueryFragment($url));

            $srcset[] = "$url $config";
        }

        $element->setAttribute('srcset', implode(', ', $srcset));
    }
}
