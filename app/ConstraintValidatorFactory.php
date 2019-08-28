<?php

namespace Filesharing;

use Symfony\Component\Validator\ConstraintValidatorFactory as SymfonyConstraintValidatorFactory;
use Symfony\Component\Validator\ConstraintValidatorInterface;

class ConstraintValidatorFactory extends SymfonyConstraintValidatorFactory
{


    public function addValidator($className, $validator): void
    {
        $this->validators[$className] = $validator;
    }
}