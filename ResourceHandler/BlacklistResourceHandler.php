<?php

namespace Baqend\Component\Spider\ResourceHandler;

use Baqend\Component\Spider\Resource;
use Baqend\Component\Spider\UrlHelper;

/**
 * Class BlacklistResourceHandler created on 2018-03-14.
 *
 * @author  Konstantin Simon Maria Möllers
 * @package Baqend\Component\Spider\ResourceHandler
 */
class BlacklistResourceHandler implements ResourceHandlerInterface
{

    /**
     * @var array
     */
    private $blacklist;

    /**
     * @var ResourceHandlerInterface
     */
    private $next;

    /**
     * BlacklistResourceHandler constructor.
     *
     * @param string[] $blacklist A blacklist to check the resource's URL against.
     * @param ResourceHandlerInterface|null $next The next handler to handle the resource.
     */
    public function __construct(array $blacklist, ResourceHandlerInterface $next = null) {
        $this->blacklist = array_map([UrlHelper::class, 'globToRegExp'], $blacklist);
        $this->next = $next;
    }

    /**
     * Handles a resource and returns a resource to be processed.
     *
     * Handling can be actions like storing a resource to disk or sending it to some web cache.
     * Returns to the spider whether to process
     *
     * @param Resource $resource The resource to handle.
     * @return Resource|null The handled resource that should processed or null, if it should not.
     */
    public function handle(Resource $resource) {
        if ($this->matchesBlacklist($resource)) {
            return null;
        }

        if ($this->next !== null) {
            return $this->next->handle($resource);
        }

        return $resource;
    }

    /**
     * @param Resource $resource A resource to match against the blacklist.
     * @return bool True, if the blacklist is matched.
     */
    private function matchesBlacklist(Resource $resource) {
        $url = $resource->getUrl();
        foreach ($this->blacklist as $pattern) {
            if (preg_match($pattern, $url) === 1) {
                return true;
            }
        }

        return false;
    }
}
