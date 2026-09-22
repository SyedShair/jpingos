<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    
protected $fillable = [
    'site_name', 'logo', 'banner_one', 'banner_two',
    'vat_number', 'address', 'map_url', 'email', 'phone', 'whatsapp', 'instagram', 'facebook',
    'opening_hours', 'show_delivery_checker',
];

    protected $casts = [
        'opening_hours'          => 'array',
        'show_delivery_checker'  => 'boolean',
    ];

    public const DAYS = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

    // This table only ever has one meaningful row — every read/write in
    // the app should go through this instead of Setting::find($id),
    // so nothing has to know or care what that row's id happens to be.
    public static function current(): self
    {
        return static::firstOrCreate([], [
            'opening_hours' => collect(self::DAYS)->mapWithKeys(fn ($day) => [
                $day => ['open' => '09:00', 'close' => '22:00', 'closed' => false],
            ])->all(),
            'show_delivery_checker' => true,
        ]);
    }

    /**
 * Collapses consecutive days sharing identical hours into one line
 * — e.g. Monday–Friday all "16:00–22:30" becomes one "Mon – Fri" row
 * instead of five separate ones. Days are compared in DAYS order
 * (Monday first), so a run has to be consecutive in that order to
 * merge; Sat/Sun sharing hours with each other but not with the
 * weekdays still splits into its own group, matching your example.
 *
 * Returns an array of ['label' => 'Mon – Fri' | 'Monday', 'hours' => '16:00 – 22:30' | 'Closed'].
 */
public function groupedOpeningHours(): array
{
    $hours = $this->opening_hours ?? [];
    $groups = [];
    $current = null;

    foreach (self::DAYS as $day) {
        $entry = $hours[$day] ?? ['open' => null, 'close' => null, 'closed' => true];

        $signature = ($entry['closed'] ?? false)
            ? 'closed'
            : ($entry['open'] ?? '') . '-' . ($entry['close'] ?? '');

        if ($current && $current['signature'] === $signature) {
            $current['days'][] = $day;
        } else {
            if ($current) {
                $groups[] = $current;
            }
            $current = ['signature' => $signature, 'days' => [$day], 'entry' => $entry];
        }
    }

    if ($current) {
        $groups[] = $current;
    }

    return array_map(function ($group) {
        $days = $group['days'];

        $label = count($days) === 1
            ? ucfirst($days[0])
            : ucfirst(substr($days[0], 0, 3)) . ' – ' . ucfirst(substr(end($days), 0, 3));

        $entry = $group['entry'];
        $hoursLabel = ($entry['closed'] ?? false)
            ? 'Closed'
            : ($entry['open'] ?? '') . ' – ' . ($entry['close'] ?? '');

        return ['label' => $label, 'hours' => $hoursLabel];
    }, $groups);
}
}