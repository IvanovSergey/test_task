<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ConstraintsCoupon extends Constraint
{
    public string $message = '';
    public array $coupons = [];

    // all configurable options must be passed to the constructor
    public function __construct($coupons, string $message = null, array $groups = null, $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->message = $message ?? $this->message;
        $this->coupons = $coupons ?? $this->coupons;
    }
}
