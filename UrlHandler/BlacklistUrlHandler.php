<?php

namespace Baqend\Component\Spider\UrlHandler;

use Baqend\Component\Spider\UrlHelper;

/**
 * Class BlacklistUrlHandler created on 2018-03-14.
 *
 * @author  Konstantin Simon Maria Möllers
 * @package Baqend\Component\Spider\UrlHandler
 */
class BlacklistUrlHandler extends AbstractChainableUrlHandler
{

    /**
     * @var string[]
     */
    private $blacklist;

    /**
     * BlacklistUrlHandler constructor.
     *
     * @param string[] $blacklist A blacklist to check the asset's URL against.
     * @param UrlHandlerInterface|null $next The next handler to handle the asset.
     */
    public function __construct(array $blacklist, UrlHandlerInterface $next = null) {
        parent::__construct($next);
        $this->blacklist = array_map([UrlHelper::class, 'globToRegExp'], $blacklist);
    }

    /**
     * Handles a URL and returns if it should be further processed.
     *
     * @param string $url The URL to handle.
     * @return boolean True, if asset should be processed or false, if it should not.
     */
    public function handle($url) {
        if ($this->matchesBlacklist($url)) {
            return false;
        }

        return $this->next($url);
    }

    /**
     * Matches the given URL against a blacklist.
     *
     * @param string $url The URL to match.
     * @return bool True, if the blacklist is matched.
     */
    public function matchesBlacklist($url) {
        foreach ($this->blacklist as $pattern) {
            if (preg_match($pattern, UrlHelper::stripQueryFragment($url)) === 1) {
                return true;
            }
        }

        return false;
    }
}
