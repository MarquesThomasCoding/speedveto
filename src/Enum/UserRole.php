<?php

namespace App\Enum;

enum UserRole: string
{
    case ROLE_USER = 'ROLE_USER';
    case ROLE_ASSISTANT = 'ROLE_ASSISTANT';
    case ROLE_VETERINARIAN = 'ROLE_VETERINARIAN';
    case ROLE_DIRECTOR = 'ROLE_DIRECTOR';

    public function getLabel(): string
    {
        return match($this) {
            self::ROLE_USER => 'Utilisateur',
            self::ROLE_ASSISTANT => 'Assistant',
            self::ROLE_VETERINARIAN => 'Vétérinaire',
            self::ROLE_DIRECTOR => 'Directeur',
        };
    }

    public static function getChoices(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_map(fn($case) => $case->getLabel(), self::cases())
        );
    }

    public static function getDefaultRole(): string
    {
        return self::ROLE_USER->value;
    }
} 