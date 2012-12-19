<?php

namespace Model\Vo;
use Model\Filter\FilterableInterface;
use Model\Validator\ValidatableInterface;

interface VoInterface extends FilterableInterface, ValidatableInterface
{
    public function set($value);

    public function get();

    public function exists();

    public function remove();
}