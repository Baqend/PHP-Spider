<?php

namespace Baqend\Component\Spider\UrlHandler;

/**
 * Class AbstractChainableUrlHandler created on 2018-03-16.
 *
 * @author  Konstantin Simon Maria Möllers
 * @package Baqend\Component\Spider\Processor
 */
abstract class AbstractChainableUrlHandler implements UrlHandlerInterface
{

    /**
     * @var UrlHandlerInterface|null
     */
    private $next;

    /**
     * AbstractChainableUrlHandler constructor.
     *
     * @param UrlHandlerInterface|null $next The next URL handler to call.
     */
    public function __construct(UrlHandlerInterface $next = null) {
        $this->next = $next;
    }

    /**
     * Lets the next chained URL handler return if the URL should be further processed.
     *
     * @param string $url The URL to handle.
     * @return boolean True, if asset should be processed or false, if it should not.
     */
    protected function next($url) {
        if ($this->next === null) {
            return true;
        }

        return $this->next->handle($url);
    }
}
