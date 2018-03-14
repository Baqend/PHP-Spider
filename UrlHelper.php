<?php

namespace Baqend\Component\Spider;

/**
 * Class UrlHelper created on 2018-03-14.
 *
 * @author  Konstantin Simon Maria Möllers
 * @package Baqend\Component\Spider
 */
class UrlHelper
{

    /**
     * Converts a glob expression to a regular expression.
     *
     * @param string $glob A glob string to be converted.
     * @param bool $caseInsensitive Whether to match case insensitively.
     * @return string A regular expression to be matched by preg_match.
     */
    public static function globToRegExp($glob, $caseInsensitive = false) {
        $regExp = '';

        $flags = $caseInsensitive ? 'i' : '';
        $inGroup = false;

        $len = strlen($glob);
        for ($i = 0; $i < $len; $i += 1) {
            $c = $glob[$i];

            switch ($c) {
                // Characters which need to be escaped
                case '/':
                case '$':
                case '^':
                case '+':
                case '.':
                case '(':
                case ')':
                case '=':
                case '!':
                case '|':
                    $regExp .= "\\$c";
                    break;

                case '?':
                    $regExp .= ".";
                    break;

                case '[':
                case ']':
                    $regExp .= $c;
                    break;

                case '{':
                    $inGroup = true;
                    $regExp .= '(?:';
                    break;

                case '}':
                    $inGroup = false;
                    $regExp .= ')';
                    break;

                case ' ':
                    if ($inGroup) {
                        break;
                    }

                case ',':
                    if ($inGroup) {
                        $regExp .= '|';
                        break;
                    }
                    $regExp .= "\\$c";
                    break;

                case '*':
                    // Move over all consecutive "*"'s.
                    // Also store the previous and next characters
                    $prevChar = $i > 0 ? $glob[$i - 1] : null;
                    $starCount = 1;
                    while ($glob[$i + 1] === "*") {
                        $starCount += 1;
                        $i += 1;
                    }
                    $nextChar = $i < strlen($glob) - 1 ? $glob[$i + 1] : null;

                    // $globstar is enabled, so determine if this is a $globstar segment
                    $isGlobstar = $starCount > 1                      // multiple "*"'s
                        && ($prevChar === '/' || $prevChar === null)   // from the start of the segment
                        && ($nextChar === '/' || $nextChar === null);  // to the end of the segment

                    if ($isGlobstar) {
                        // it's a $globstar, so match zero or more path segments
                        $regExp .= '(?:[^\\/]*(?:\\/|$))*';
                        $i += 1; // move over the '/'
                    } else {
                        // it's not a $globstar, so only match one path segment
                        $regExp .= '[^\\/]*';
                    }
                    break;

                default:
                    $regExp .= $c;
            }
        }

        // Constrain the regular expression with ^ & $
        return "/^$regExp$/$flags";
    }

    /**
     * @param string $url
     * @param string $toResolve
     * @return string
     */
    public static function resolve($url, $toResolve) {
        if (empty($toResolve)) {
            return $url;
        }

        // Check if toResolve is absolute
        $toResolveSchema = self::extractSchema($toResolve);
        if ($toResolveSchema !== null) {
            return $toResolve;
        }

        // Is it an absolute "//" URL?
        if ($toResolve[0] === '/') {
            // Is it a "//" URL?
            if (strlen($toResolve) > 1 && $toResolve[1] === '/') {
                return self::extractSchema($url).':'.$toResolve;
            }

            return self::extractOrigin($url).$toResolve;
        }

        list($origin, $path) = self::extractOriginAndPath($url);
        $path = ltrim($path, '/');

        $segments = empty($path) ? [] : explode('/', preg_replace('#/+#', '/', $path));
        $toResolve = preg_replace('#/+#', '/', $toResolve);
        $stripFilename = true;

        foreach (explode('/', $toResolve) as $segment) {
            if ($segment === '..') {
                if (array_pop($segments) === null) {
                    return null;
                }
                continue;
            }

            if ($segment === '.') {
                continue;
            }

            if ($segment && $stripFilename) {
                array_pop($segments);
                $stripFilename = false;
            }
            $segments[] = $segment;
        }

        return $origin.'/'.implode('/', $segments);
    }

    /**
     * Extracts the schema of a URL, e.g. "https".
     *
     * @param string $url A URL to get the schema of.
     * @return null|string The schema or null, if it is not part of the URL.
     */
    public static function extractSchema($url) {
        return preg_match('#^(https?)://#', $url, $matches) === 1 ? $matches[1] : null;
    }

    /**
     * Extracts the origin of a URL, e.g. "https://www.exmaple.org".
     *
     * @param string $url A URL to get the origin of.
     * @return null|string The origin or null, if it is not part of the URL.
     */
    public static function extractOrigin($url) {
        return preg_match('#^(https?://[^/]+)#', $url, $matches) === 1 ? $matches[1] : null;
    }

    /**
     * Extracts the path of a URL, e.g. "/my/absolute/path.html".
     *
     * @param string $url A URL to get the path of.
     * @return null|string The path or null, if it is not part of the URL.
     */
    public static function extractPath($url) {
        return preg_match('#^https?://[^/]+(.*)$#', $url, $matches) === 1 ? $matches[1] : null;
    }

    /**
     * Extracts the origin and path of a URL, e.g. ["https://www.exmaple.org", "/my/absolute/path.html"].
     *
     * @param string $url A URL to get the origin and path of.
     * @return array The origin and path in a tuple or two nulls in a tuple, if it is not part of the URL.
     */
    public static function extractOriginAndPath($url) {
        return preg_match('#^(https?://[^/]+)(.*)$#', $url, $matches) === 1 ? [$matches[1], $matches[2]] : [null, null];
    }
}
