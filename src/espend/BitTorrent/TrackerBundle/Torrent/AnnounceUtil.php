<?php

namespace espend\BitTorrent\TrackerBundle\Torrent;

class AnnounceUtil
{
    /**
     * @param array $query
     * @param string $origin
     * @return string
     */
    public static function mergeUrlQuery($origin, array $query)
    {
        parse_str(parse_url($origin, PHP_URL_QUERY), $parts);

        if (count($parts) > 0) {
            $origin = strstr($origin, '?', true);
            $query = array_merge($parts, $query);
        }

        return $origin . '?' . http_build_query($query);
    }
}