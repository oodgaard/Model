<?php

namespace Provider;

class ContentValidator
{
    public function __invoke(ContentEntity $content)
    {
        $content->validatedUsingClass = true;
    }
}