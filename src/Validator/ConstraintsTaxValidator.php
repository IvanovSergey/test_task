<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class ConstraintsTaxValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ConstraintsTax) {
            throw new UnexpectedTypeException($constraint, ConstraintsTax::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        $country = substr($value, 0, 2);
        
        switch ($country) {
            case 'DE':
                if(strlen($value) !== 11) {
                    $constraint->message = 'German tax number should have 9 numbers.';
                }
                break;
            case 'IT':
                if(strlen($value) !== 13) {
                    $constraint->message = 'Italian tax number should have 11 numbers.';
                }
                break;
            case 'FR':
                $rest = substr($value, 2, 2);
                if(!ctype_alpha($rest)) {
                    $constraint->message = 'Third and Fourth characters should be letters in French tax number.';
                }
                if(strlen($value) !== 13) {
                    $constraint->message = 'French tax number should have 9 numbers.';
                }
                break;
            case 'GR':
                if(strlen($value) !== 11) {
                    $constraint->message = 'Greece tax number should have 9 numbers.';
                }
                break;
            default:
                $constraint->message = 'No such tax number.';
        }

        if(!empty($constraint->message)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }
}

