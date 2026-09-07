<?php

namespace App\Http\Requests;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MenuItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'               => ['required', 'string', 'max:150'],
            'description'        => ['nullable', 'string', 'max:2000'],
            'category_id'        => [
                'required',
                Rule::exists('categories', 'id'),
                function ($attribute, $value, $fail) {
                    if (Category::whereKey($value)->whereHas('children')->exists()) {
                        $fail('Dishes must be assigned to a sub-category, not a main category.');
                    }
                },
            ],
            'price'              => ['required', 'numeric', 'min:0'],
            'discount_price'     => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'prep_time_minutes'  => ['nullable', 'integer', 'min:0', 'max:600'],
            'spice_level'        => ['required', 'in:'.implode(',', array_keys(MenuItem::spiceLevels()))],
            'is_vegetarian'      => ['nullable', 'boolean'],
            'is_vegan'           => ['nullable', 'boolean'],
            'is_gluten_free'     => ['nullable', 'boolean'],
            'ingredients'        => ['nullable', 'string', 'max:2000'],
            'is_available'       => ['nullable', 'boolean'],
            'is_featured'        => ['nullable', 'boolean'],

            // Existing, reusable option groups selected via checkbox.
            'option_group_ids'   => ['nullable', 'array'],
            'option_group_ids.*' => ['integer', 'exists:option_groups,id'],

            // Brand-new option groups created inline — these get saved to
            // the shared library AND attached to this dish.
            'new_options'                        => ['nullable', 'array'],
            'new_options.*.name'                 => ['required', 'string', 'max:100', 'distinct', Rule::unique('option_groups', 'name')],
            'new_options.*.selection_type'       => ['required', 'in:single,multiple'],
            'new_options.*.required'             => ['nullable', 'boolean'],
            'new_options.*.min_select'           => ['nullable', 'integer', 'min:0'],
            'new_options.*.max_select'           => ['nullable', 'integer', 'min:1'],
            'new_options.*.values'               => ['required', 'array', 'min:1'],
            'new_options.*.values.*.name'        => ['required', 'string', 'max:100'],
            'new_options.*.values.*.price_delta' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'new_options.*.name.required'         => 'Every new option needs a name (or remove the empty row).',
            'new_options.*.name.unique'            => 'An option named ":input" already exists — select it from the list above instead of creating it again.',
            'new_options.*.name.distinct'          => 'You entered the same new option name twice.',
            'new_options.*.values.required'        => 'Add at least one choice for each new option, or remove it.',
            'new_options.*.values.*.name.required' => 'Every choice needs a name (or remove the empty row).',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            foreach ($this->input('new_options', []) as $index => $group) {
                if (($group['selection_type'] ?? 'single') !== 'multiple') {
                    continue;
                }

                $min = (int) ($group['min_select'] ?? 0);
                $max = (int) ($group['max_select'] ?? 1);
                $valueCount = count(array_filter(
                    $group['values'] ?? [],
                    fn ($v) => trim($v['name'] ?? '') !== ''
                ));

                if ($max < $min) {
                    $validator->errors()->add(
                        "new_options.{$index}.max_select",
                        'Max selections can\'t be less than min selections.'
                    );
                }

                if ($max > $valueCount && $valueCount > 0) {
                    $validator->errors()->add(
                        "new_options.{$index}.max_select",
                        "Max selections ({$max}) can't exceed the number of choices ({$valueCount})."
                    );
                }
            }
        });
    }
}