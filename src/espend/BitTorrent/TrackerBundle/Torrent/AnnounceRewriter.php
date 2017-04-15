<?php

namespace espend\BitTorrent\TrackerBundle\Torrent;

use PHP\BitTorrent\Torrent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class AnnounceRewriter
{
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param Torrent $torrent
     */
    public function rewriteAnnounceUrls(Torrent $torrent)
    {
        // single tracker
        if ($announce = $torrent->getAnnounce()) {
            $torrent->setAnnounce($this->router->generate('espend_bit_torrent_tracker_proxy_announce', [
                'origin' => base64_encode($announce),
            ], UrlGeneratorInterface::ABSOLUTE_URL));
        }

        // tracker list
        $torrent->setAnnounceList(array_map(function ($trackers) {
            return array_map(function ($tracker) {
                return $this->router->generate('espend_bit_torrent_tracker_proxy_announce', [
                    'origin' => base64_encode($tracker),
                ], UrlGeneratorInterface::ABSOLUTE_URL);
            }, $trackers);
        }, $torrent->getAnnounceList()));
    }
}