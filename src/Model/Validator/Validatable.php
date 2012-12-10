<?php

namespace Model\Validator;
use InvalidArgumentException;

trait Validatable
{
    private $validatorMessages = [];

    private $validators = [];

    public function addValidator($message, callable $validator)
    {
        $this->validatorMessages[] = $message;
        $this->validators[]        = $validator;
        return $this;
    }

    public function getValidatorMessages()
    {
        return $this->validatorMessages;
    }

    public function getValidators()
    {
        return $this->validators;
    }
}