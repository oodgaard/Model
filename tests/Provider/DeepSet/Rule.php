<?php

namespace Provider\DeepSet;
use Model\Entity;

class Rule extends Entity\Entity
{
    /**
     * @vo Model\Vo\HasMany 'Provider\DeepSet\IpAddress'
     *
     * @autoload loadIpAddress
     */
    public $ipAddresses;

    public function loadIpAddress()
    {
        $ip = new IpAddress(['ipAddress' => '10.0.0.1']);
        return new Entity\Set('Provider\DeepSet\IpAddress', [$ip]);
    }
}
