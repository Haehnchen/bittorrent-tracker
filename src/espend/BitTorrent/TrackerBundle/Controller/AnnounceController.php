<?php

namespace espend\BitTorrent\TrackerBundle\Controller;

use espend\BitTorrent\TrackerBundle\Response\PeerListResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class AnnounceController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        return new PeerListResponse([
            [
                'ip' => '127.0.0.1',
                'port' => 1337,
            ]
        ]);
    }
}
