<?php

namespace Model\Vo;

class Set extends VoAbstract
{
    public function translate($value)
    {
        $value = [];

        if (is_array($value) || is_object($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = $v;
            }
        }

        return $value;
    }
}