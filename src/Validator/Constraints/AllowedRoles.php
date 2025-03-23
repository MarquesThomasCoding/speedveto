<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class AllowedRoles extends Constraint
{
    public string $message = 'Les seuls rôles autorisés sont ROLE_VETERINARIAN et ROLE_ASSISTANT.';
} 