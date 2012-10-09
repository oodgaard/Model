<?php

namespace Model\Vo;
use Model\Validator\Validatable;
use Model\Validator\ValidatableInterface;

/**
 * The base value object interface.
 * 
 * @category ValueObjects
 * @package  Model
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2010 Trey Shugart http://europaphp.org/license
 */
abstract class VoAbstract implements VoInterface
{
    use Validatable;
    
    /**
     * Validates the entity and returns the error messages.
     * 
     * @return array
     */
    public function validate()
    {
        $messages = [];
        $value    = $this->get();
        
        foreach ($this->getValidators() as $message => $validator) {
            if (call_user_func($validator, $this->get()) === false) {
                $messages[] = $message;
            }
        }
        
        if ($value instanceof ValidatableInterface) {
            $messages = array_merge($messages, $value->validate());
        }
        
        return $messages;
    }
}