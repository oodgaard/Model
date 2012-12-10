<?php

namespace Model\Validator;
use ArrayAccess;
use ArrayIterator;
use Exception;
use IteratorAggregate;

class ValidatorException extends Exception implements ArrayAccess, IteratorAggregate
{
    private $messages = [];
    
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

    public function addMessage($message)
    {
        $this->messages[] = $message;
        return $this;
    }
    
    public function addMessages(array $messages)
    {
        $this->messages = array_merge($this->messages, $messages);
        return $this;
    }

    public function getMessages()
    {
        return $this->messages;
    }

    public function offsetSet($offset, $message)
    {
        $this->messages[$offset] = $message;
        return $this;
    }

    public function offsetGet($offset)
    {
        if (isset($this->messages[$offset])) {
            return $this->messages[$offset];
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->messages[$offset]);
    }

    public function offsetUnset($offset)
    {
        if (isset($this->messages[$offset])) {
            unset($this->messages[$offset]);
        }
    }
    
    public function getIterator()
    {
        return new ArrayIterator($this->messages);
    }
}