<?php

namespace App\Services;

use App\Models\MenuItem;
use App\Models\OptionGroup;
use App\Models\OptionValue;
use Illuminate\Support\Facades\DB;

class MenuItemOptionService
{
    /**
     * Attach this dish's chosen existing option groups, and create +
     * attach any brand-new ones typed inline on the dish form. New
     * groups are saved to the shared library so future dishes can
     * select them too, instead of retyping them.
     */
    public function sync(MenuItem $item, array $existingGroupIds, array $newGroupsInput): void
    {
        DB::transaction(function () use ($item, $existingGroupIds, $newGroupsInput) {
            $attachIds = array_map('intval', $existingGroupIds);

            foreach (array_values($newGroupsInput) as $groupData) {
                $name = trim($groupData['name'] ?? '');
                if ($name === '') {
                    continue;
                }

                $isSingle = ($groupData['selection_type'] ?? 'single') === 'single';

                $group = OptionGroup::create([
                    'name'           => $name,
                    'selection_type' => $isSingle ? 'single' : 'multiple',
                    'min_select'     => $isSingle
                        ? (! empty($groupData['required']) ? 1 : 0)
                        : max(0, (int) ($groupData['min_select'] ?? 0)),
                    'max_select'     => $isSingle ? 1 : max(1, (int) ($groupData['max_select'] ?? 1)),
                    'sort_order'     => OptionGroup::max('sort_order') + 1,
                ]);

                $now = now();
                $values = [];

                foreach (array_values($groupData['values'] ?? []) as $valueIndex => $valueData) {
                    $valueName = trim($valueData['name'] ?? '');
                    if ($valueName === '') {
                        continue;
                    }

                    $values[] = [
                        'option_group_id' => $group->id,
                        'name'            => $valueName,
                        'price_delta'     => $valueData['price_delta'] ?? 0,
                        'sort_order'      => $valueIndex,
                        'created_at'      => $now,
                        'updated_at'      => $now,
                    ];
                }

                if (! empty($values)) {
                    OptionValue::insert($values);
                }

                $attachIds[] = $group->id;
            }

            // Pivot sync — assigns sort_order by the position each ID
            // appears in $attachIds, so the dish's display order roughly
            // follows the order the admin picked/created them in.
            $syncData = [];
            foreach (array_values(array_unique($attachIds)) as $position => $groupId) {
                $syncData[$groupId] = ['sort_order' => $position];
            }

            $item->optionGroups()->sync($syncData);
        });
    }
}