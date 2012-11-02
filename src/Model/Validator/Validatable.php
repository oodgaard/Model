<?php

namespace Model\Validator;
use InvalidArgumentException;

/**
 * Enables a class to have validators and be validated.
 * 
 * @category Validators
 * @package  Model
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2011 Trey Shugart http://europaphp.org/license
 */
trait Validatable
{
    /**
     * The validators applied to the entity.
     * 
     * @var array
     */
    private $validators = [];
    
    /**
     * Adds a validator to the entity.
     * 
     * @param string   $message   The message to add.
     * @param callable $validator The entity validator.
     * 
     * @return Entity
     */
    public function addValidator($message, callable $validator)
    {
        $this->validators[$message] = $validator;
        return $this;
    }
    
    /**
     * Returns the messages and validators.
     * 
     * @return array
     */
    public function getValidators()
    {
        return $this->validators;
    }
}