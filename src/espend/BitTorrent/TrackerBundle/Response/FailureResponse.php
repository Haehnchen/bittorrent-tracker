<?php

namespace espend\BitTorrent\TrackerBundle\Response;

class FailureResponse extends TrackerResponse
{
    public function __construct($content = '')
    {
        parent::__construct($content, 400, []);
    }

    public function setContent($content)
    {
        return parent::setContent(['failure reason' => (string) $content]);
    }
}