<?php

namespace Model\Vo;
use Model\Util\DotNotatedArray;
use Model\Filter\Filterable;
use Model\Validator\Validatable;

abstract class VoAbstract implements VoInterface
{
    use Filterable;
    
    use Validatable;

    public function init()
    {
        
    }

    public function from($value, $filterToUse = null)
    {
        foreach ($this->getImportFilters()->offsetGet($filterToUse) as $filter) {
            $value = $filter($value);
        }

        return $value;
    }

    public function to($value, $filterToUse = null)
    {
        foreach ($this->getExportFilters()->offsetGet($filterToUse) as $filter) {
            $value = $filter($value);
        }

        return $value;
    }

    public function validate($value)
    {
        $messages = [];

        foreach ($this->getValidators() as $message => $validator) {
            if ($validator($value) === false) {
                $messages[] = $this->validatorMessages[$message];
            }
        }
        
        if ($value instanceof ValidatableInterface) {
            $messages = array_merge($messages, $value->validate($value));
        }
        
        return $messages;
    }
}