<?php

namespace Model\Validator;
use Model\Entity\Entity;

interface ValidatorInterface
{
    public function validate(Entity $entity);
}