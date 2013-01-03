<?php

namespace Provider;

class ContentRepository extends BaseRepository
{
    /**
     * @cache Using PHP.
     * 
     * @ensure Provider\ContentEntity
     */
    protected function findById($id)
    {
        return parent::findById($id);
    }
}