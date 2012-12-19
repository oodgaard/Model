<?php

namespace Model\Vo;
use Model\Util\DotNotatedArray;
use Model\Filter\Filterable;
use Model\Validator\Validatable;

abstract class VoAbstract implements VoInterface
{
    use Filterable;
    
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

    public function from($value, $filterToUse)
    {
        foreach ($this->getImportFilters()->offsetGet($filterToUse) as $filter) {
            $value = $filter($value);
        }

        $this->set($value);

        return $this;
    }

    public function to($filterToUse)
    {
        $value = $this->get();

        foreach ($this->getExportFilters()->offsetGet($filterToUse) as $filter) {
            $value = $filter($value);
        }

        return $value;
    }
}