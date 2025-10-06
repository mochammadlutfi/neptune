<?php

namespace App\Services;

use App\Models\Sequence;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SequenceGenerator
{
    public static function generate(string $sequenceName): string
    {
        return DB::transaction(function () use ($sequenceName) {
            $sequence = Sequence::where('name', $sequenceName)->lockForUpdate()->first();

            if (!$sequence) {
                throw new \Exception("Sequence with name {$sequenceName} not found.");
            }

            // Cek apakah reset perlu dilakukan
            $today = Carbon::today();
            if (self::needsReset($sequence, $today)) {
                $sequence->current_number = 0; // Reset nomor
                $sequence->last_reset_date = $today;
            }

            // Increment nomor
            $sequence->current_number += 1;
            $sequence->save();

            // Format tanggal sesuai format di database
            $dateFormat = $sequence->date_format ?: 'ymd'; // Default ymd
            $datePart = $today->format(self::convertDateFormat($dateFormat));

            // Buat nomor sequence
            $number = str_pad($sequence->current_number, $sequence->padding, '0', STR_PAD_LEFT);

            // Gabungkan prefix, tanggal, dan nomor
            return "{$sequence->prefix}/{$datePart}/{$number}";
        });
    }

    private static function needsReset(Sequence $sequence, Carbon $today): bool
    {
        if (!$sequence->reset_period || $sequence->reset_period === 'none') {
            return false;
        }

        $lastReset = $sequence->last_reset_date ? Carbon::parse($sequence->last_reset_date) : null;

        switch ($sequence->reset_period) {
            case 'daily':
                return !$lastReset || !$lastReset->isSameDay($today);
            case 'monthly':
                return !$lastReset || !$lastReset->isSameMonth($today);
            case 'yearly':
                return !$lastReset || !$lastReset->isSameYear($today);
            default:
                return false;
        }
    }

    private static function convertDateFormat(string $format): string
    {
        $map = [
            'dmy' => 'dmy',
            'ymd' => 'ymd',
            'ym'  => 'ym',
            'my'  => 'my',
        ];

        return $map[$format] ?? 'ymd';
    }
}
