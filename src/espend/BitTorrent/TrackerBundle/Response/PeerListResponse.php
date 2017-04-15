<?php

namespace espend\BitTorrent\TrackerBundle\Response;

class PeerListResponse extends TrackerResponse
{
    /**
     * @var array
     */
    private $peers;

    /**
     * @var int
     */
    private $interval;

    /**
     * @var bool
     */
    private $compact;

    /**
     * PeerListResponse constructor.
     *
     * @param array $peers
     * @param int $interval
     * @param bool $compact
     */
    public function __construct(array $peers, $interval = 300, $compact = true)
    {
        $this->peers = $peers;
        $this->interval = $interval;
        $this->compact = $compact;

        parent::__construct();
    }

    public function setContent($content)
    {
        $response = [
            'interval' => $this->interval,
        ];

        if($this->compact) {
            $response['peers'] = '';

            foreach ($this->peers as $peer) {
                if (!isset($peer['ip'], $peer['port'])) {
                    continue;
                }

                $response['peers'] .= pack("Nn", ip2long($peer['ip']), $peer['port']);
            }
        } else {
            $response['peers'] = $this->peers;
        }

        return parent::setContent($response);
    }
}