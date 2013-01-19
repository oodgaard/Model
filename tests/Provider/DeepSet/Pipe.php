<?php

namespace Provider\DeepSet;
use Model\Entity;

class Pipe extends Entity\Entity
{
    /**
     * @vo Model\Vo\HasMany 'Provider\DeepSet\Rule'
     *
     * @autoload loadRules
     */
    public $rules;

    public function loadRules()
    {
        return new Entity\Set('Provider\DeepSet\Rule', [new Rule()]);
    }
}
