<?php

namespace Model\Validator;
use ArrayIterator;
use Exception;
use IteratorAggregate;

/**
 * Validator exception.
 * 
 * @category Validators
 * @package  Model
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2011 Trey Shugart http://europaphp.org/license
 */
class ValidatorException extends Exception implements IteratorAggregate
{
    /**
     * The exception messages.
     * 
     * @var array
     */
    private $messages = [];
    
    /**
     * Converts the validation exception to a string.
     * 
     * @return string
     */
    public function __toString()
    {
        $out = $this->getMessage();
        
        if ($out) {
            $out .= PHP_EOL . PHP_EOL;
        }
        
        foreach ($this->messages as $message) {
            $out .= '- ' . $message . PHP_EOL;
        }
        
        return $out . PHP_EOL . $this->getTraceAsString();
    }
    
    /**
     * Adds a message to the exception.
     * 
     * @param string $message The message to add.
     * 
     * @return ValidatorException
     */
    public function addMessage($message)
    {
        $this->messages[] = $message;
        return $this;
    }
    
    /**
     * Adds multiple messages to the exception.
     * 
     * @param array $messages The messages to add.
     * 
     * @return ValidatorException
     */
    public function addMessages(array $messages)
    {
        $this->messages = array_merge($this->messages, $messages);
        return $this;
    }
    
    /**
     * Returns an interator that can be used to iterate over the exceptions.
     * 
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->messages);
    }
}