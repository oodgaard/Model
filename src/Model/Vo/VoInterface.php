<?php

namespace Model\Vo;
use Model\Validator\ValidatableInterface;

interface VoInterface
{
    public function init();

    public function translate($value);

    public function from($value, $filter = null);

    public function to($value, $filter = null);

    public function validate($value);
}