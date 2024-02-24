<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ConstraintsPaymentProcessor extends Constraint
{
    public string $message = '';
    public array $processors = [];

    // all configurable options must be passed to the constructor
    public function __construct($coupons, string $message = null, array $groups = null, $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->message = $message ?? $this->message;
        $this->processors = $coupons ?? $this->processors;
    }
}
