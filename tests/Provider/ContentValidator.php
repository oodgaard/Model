<?php

namespace Provider;

class ContentValidator
{
    public function __invoke(ContentEntity $content)
    {
        ContentEntity::$validatedUsingClass = true;
    }
}