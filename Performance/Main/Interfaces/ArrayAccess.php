<?php

namespace PF\Main\Interfaces;

interface ArrayAccess extends \ArrayAccess {
    
    public function arrayShift();

    public function arrayUnshift($value);

    public function toArray();

    public function fromArray($array);
}
