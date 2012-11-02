<?php

namespace Model\Validator;

/**
 * Enables a class to have validators and be validated.
 * 
 * @category Validators
 * @package  Model
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2011 Trey Shugart http://europaphp.org/license
 */
interface ValidatableInterface
{
    /**
     * Adds a validator to the entity.
     * 
     * @param string   $message   The message to add.
     * @param callable $validator The callable validator.
     * 
     * @return mixed
     */
    public function addValidator($message, callable $validator);
    
    /**
     * Validates the instance against each added validator and returns the error messages.
     * 
     * @return array
     */
    public function validate();
    
    /**
     * Returns the messages and validators.
     * 
     * @return array
     */
    public function getValidators();
}