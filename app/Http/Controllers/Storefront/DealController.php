<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Deal;

class DealController extends Controller
{
    private const TYPE_LABELS = [
        'flash_deal'    => 'Flash Deal',
        'happy_hour'    => 'Happy Hour',
        'lunch_special' => 'Lunch Special',
        'tiered_spend'  => 'Spend & Save',
        'bogo'          => 'Buy One Get One',
        'free_gift'     => 'Free Gift',
        'combo'         => 'Combo',
        'bundle'        => 'Bundle',
        'promo_code'    => 'Promo Code',
    ];

    public function index()
    {
        $search = trim((string) request('search'));
        $type = request('type');
        $sort = request('sort', 'default');

        $perPage = (int) request('per_page', 12);

        if (! in_array($perPage, [12, 24, 30], true)) {
            $perPage = 12;
        }

        $deals = Deal::query()
            ->with([
                'appliesToItems.menuItem',
                'buyItems.menuItem',
                'freeItems.menuItem',
                'bundleComponents.menuItem',
            ])
            ->where('is_active', true)

            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('promo_code', 'like', '%' . $search . '%');
                });
            })

            ->when(
                $type && array_key_exists($type, self::TYPE_LABELS),
                fn ($q) => $q->where('type', $type)
            )

            ->when(
                $sort === 'latest',
                fn ($q) => $q->latest()
            )

            ->when(
                $sort === 'default',
                fn ($q) => $q->orderBy('name')
            )

            ->paginate($perPage)
            ->withQueryString();

        $typeCounts = collect(self::TYPE_LABELS)->mapWithKeys(
            fn ($label, $key) => [
                $key => Deal::where('is_active', true)
                    ->where('type', $key)
                    ->count()
            ]
        );

        $recentDeals = Deal::where('is_active', true)
            ->latest()
            ->take(8)
            ->get();

        return view('storefront.deals', [
            'pageTitle'   => 'Deals',

            'deals'       => $deals,
            'typeLabels'  => self::TYPE_LABELS,
            'typeCounts'  => $typeCounts,
            'recentDeals' => $recentDeals,
            'search'      => $search,
            'activeType'  => $type,
            'sort'        => $sort,
            'perPage'     => $perPage,
        ]);
    }
}