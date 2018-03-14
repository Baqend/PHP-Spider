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

}
