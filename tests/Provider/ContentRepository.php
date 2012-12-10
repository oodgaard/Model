<?php

namespace Provider;

class ContentRepository extends BaseRepository
{
    /**
     * @return Provider\ContentEntity
     */
    protected function findById($id)
    {
        return parent::findById($id);
    }
}