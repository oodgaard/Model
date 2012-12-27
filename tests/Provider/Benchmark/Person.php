<?php

namespace Provider\Benchmark;
use Model\Entity\Entity;

class Person extends Entity
{
    /**
     * @vo Model\Vo\UniqueId
     * 
     * @validator Provider\Validator\Required
     */
    public $id;

    /**
     * @vo Model\Vo\String
     * 
     * @validator Provider\Validator\Required
     */
    public $forename;

    /**
     * @vo Model\Vo\String
     * 
     * @validator Provider\Validator\Required
     */
    public $surname;

    /**
     * @vo Model\Vo\String
     * 
     * @validator Provider\Validator\Required
     * @validator Provider\Validator\Email
     */
    public $email;

    /**
     * @vo Model\Vo\String
     * 
     * @filter to db using Model\Filter\To\Md5
     * 
     * @validator Provider\Validator\Required
     */
    public $password;

    /**
     * @vo Model\Vo\HasOne 'Provider\Benchmark\Address'
     */
    public $residence;

    /**
     * @vo Model\Vo\HasMany 'Provider\Benchmark\Address'
     */
    public $addresses;
}