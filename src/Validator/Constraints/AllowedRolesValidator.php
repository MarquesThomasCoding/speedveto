<?php

namespace App\Validator\Constraints;

use App\Enum\UserRole;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AllowedRolesValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (null === $value || '' === $value) {
            return;
        }

        $allowedRoles = [
            UserRole::ROLE_VETERINARIAN->value,
            UserRole::ROLE_ASSISTANT->value
        ];

        foreach ($value as $role) {
            if (!in_array($role, $allowedRoles)) {
                $this->context->buildViolation($constraint->message)
                    ->addViolation();
            }
        }
    }
} 