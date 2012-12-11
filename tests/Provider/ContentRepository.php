<?php

namespace Provider;

class ContentRepository extends BaseRepository
{
    /**
     * @cache Using PHP.
     * 
     * @return Provider\ContentEntity
     */
    protected function findById($id)
    {
        return parent::findById($id);
    }
}