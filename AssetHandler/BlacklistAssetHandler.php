<?php

namespace Baqend\Component\Spider\AssetHandler;

use Baqend\Component\Spider\Asset;
use Baqend\Component\Spider\UrlHelper;

/**
 * Class BlacklistAssetHandler created on 2018-03-14.
 *
 * @author  Konstantin Simon Maria Möllers
 * @package Baqend\Component\Spider\AssetHandler
 */
class BlacklistAssetHandler implements AssetHandlerInterface
{

    /**
     * @var array
     */
    private $blacklist;

    /**
     * @var AssetHandlerInterface
     */
    private $next;

    /**
     * BlacklistAssetHandler constructor.
     *
     * @param string[] $blacklist A blacklist to check the asset's URL against.
     * @param AssetHandlerInterface|null $next The next handler to handle the asset.
     */
    public function __construct(array $blacklist, AssetHandlerInterface $next = null) {
        $this->blacklist = array_map([UrlHelper::class, 'globToRegExp'], $blacklist);
        $this->next = $next;
    }

    /**
     * Handles a asset and returns a asset to be processed.
     *
     * Handling can be actions like storing a asset to disk or sending it to some web cache.
     * Returns to the spider whether to process
     *
     * @param Asset $asset The asset to handle.
     * @return Asset|null The handled asset that should processed or null, if it should not.
     */
    public function handle(Asset $asset) {
        if ($this->matchesBlacklist($asset)) {
            return null;
        }

        if ($this->next !== null) {
            return $this->next->handle($asset);
        }

        return $asset;
    }

    /**
     * @param Asset $asset A asset to match against the blacklist.
     * @return bool True, if the blacklist is matched.
     */
    private function matchesBlacklist(Asset $asset) {
        $url = $asset->getUrl();
        foreach ($this->blacklist as $pattern) {
            if (preg_match($pattern, $url) === 1) {
                return true;
            }
        }

        return false;
    }
}
