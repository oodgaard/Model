<?php

namespace Model\Configurator\DocComment;
use IteratorAggregate;

interface DocCommentInterface extends IteratorAggregate
{
    public function __construct($comment);
}