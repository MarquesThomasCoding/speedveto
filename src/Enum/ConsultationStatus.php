<?php

namespace App\Enum;

enum ConsultationStatus: string
{
    case PROGRAMMED = 'programmed';
    case IN_PROGRESS = 'in_progress';
    case DONE = 'done';

    public function getLabel(): string
    {
        return match($this) {
            self::PROGRAMMED => 'Programmé',
            self::IN_PROGRESS => 'En cours',
            self::DONE => 'Terminé',
        };
    }

    public static function getChoices(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_map(fn($case) => $case->getLabel(), self::cases())
        );
    }
}
