<?php

namespace Model\Vo;
use Model\Validator\Validatable;
use Model\Validator\ValidatableInterface;

abstract class VoAbstract implements VoInterface
{
    use Validatable;
    
    public function validate()
    {
        $messages = [];
        $value    = $this->get();

        foreach ($this->getValidators() as $message => $validator) {
            if ($validator($this->get()) === false) {
                $messages[] = $this->validatorMessages[$message];
            }
        }
        
        if ($value instanceof ValidatableInterface) {
            $messages = array_merge($messages, $value->validate());
        }
        
        return $messages;
    }
}