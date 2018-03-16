<?php

namespace Baqend\Component\Spider\UrlHandler;

/**
 * Class OriginUrlHandler created on 2018-03-14.
 *
 * @author  Konstantin Simon Maria Möllers
 * @package Baqend\Component\Spider\UrlHandler
 */
class OriginUrlHandler extends AbstractChainableUrlHandler
{

    private $origin;

    /**
     * OriginUrlHandler constructor.
     *
     * @param string $origin The origin which returns true for URLs.
     * @param UrlHandlerInterface|null $next The next handler to handle the asset.
     */
    public function __construct($origin, UrlHandlerInterface $next = null) {
        parent::__construct($next);
        $this->origin = $origin;
    }

    /**
     * Handles a URL and returns if it should be further processed.
     *
     * @param string $url The URL to handle.
     * @return boolean True, if asset should be processed or false, if it should not.
     */
    public function handle($url) {
        if (strpos($url, $this->origin) !== 0) {
            return false;
        }

        return $this->next($url);
    }
}
