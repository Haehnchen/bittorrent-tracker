<?php

namespace espend\BitTorrent\TrackerBundle\Response;

use PHP\BitTorrent\Encoder;
use Symfony\Component\HttpFoundation\Response;

class TrackerResponse extends Response
{
    public function setContent($content)
    {
        return parent::setContent((new Encoder())->encode($content));
    }
}