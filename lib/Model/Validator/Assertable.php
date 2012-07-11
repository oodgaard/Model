<?php

namespace Model\Validator;

/**
 * Allows asserting against validators using validator exceptions.
 * 
 * @category Validators
 * @package  Model
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2011 Trey Shugart http://europaphp.org/license
 */
trait Assertable
{
    use Validatable;
    
    /**
     * Validates and throws a ValidatorException with the returned messages if the instance is not valid.
     * 
     * @return Validatable
     * 
     * @throws ValidatorException If the instance is not valid.
     */
    public function assert()
    {
        if ($messages = $this->validate()) {
            $e = new ValidatorException;
            $e->addMessages($messages);
            throw $e;
        }
        return $this;
    }
}