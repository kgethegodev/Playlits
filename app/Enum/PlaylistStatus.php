<?php

namespace App\Enum;

enum PlaylistStatus: string
{
    case Created = 'created';
    case Dispatched = 'tracks_dispatched';
    case Complete = 'complete';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    // Optional: helper to get a label
    public function label(): string
    {
        return match ($this) {
            self::Created => 'created',
            self::Dispatched => 'tracks_dispatched',
            self::Complete => 'complete',
        };
    }

    // Optional: map for select dropdowns
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])->toArray();
    }
}
