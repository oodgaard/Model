<?php

namespace Model\Validator;

/**
 * Bluprints something that can be validated and asserted.
 * 
 * @category Validators
 * @package  Model
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2011 Trey Shugart http://europaphp.org/license
 */
interface AssertableInterface extends ValidatableInterface
{
    /**
     * Validates and throws a ValidatorException with the returned messages if the instance is not valid.
     * 
     * @return Validatable
     * 
     * @throws ValidatorException If the instance is not valid.
     */
    public function assert();
}