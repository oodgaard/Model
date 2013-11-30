<?php

namespace Provider\Cache;
use Provider\BaseRepository;
use Provider\ReferenceEntity;
use Model\Entity\Set;

class Repository extends BaseRepository
{
    /**
     * @cache Using MemCache for 10
     */
    protected function getByContentId($id)
    {
        sleep(1);

        return new Set('Provider\ReferenceEntity', [[
            'id'          => 1,
            'contentId'   => $id,
            'description' => 'Wikipedia reference 1',
            'link'        => 'http://en.wikipedia.org/wiki/Domain-driven_design',

        ], [
            'id'          => 2,
            'contentId'   => $id,
            'description' => 'Clean Code published by Prentice Hall',
            'link'        => 'http://www.amazon.com/Clean-Code-Handbook-Software-Craftsmanship/dp/0132350882',
        ]]);
    }

    /**
     * @ensure Provider\ReferenceEntity
     * @cache Using MemCache for 10
     */
    protected function getById($id)
    {
        return [
            'id'          => 1,
            'contentId'   => $id,
            'description' => 'Wikipedia reference 1',
            'link'        => 'http://en.wikipedia.org/wiki/Domain-driven_design'
        ];
    }
}