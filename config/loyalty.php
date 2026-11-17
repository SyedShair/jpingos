<?php

return [

    // Completed orders needed to earn one coupon.
    'orders_required' => 5,

    // 'percent' = take a percentage off the food subtotal (delivery fee is
    // never discounted). 'fixed' = take a flat £ amount off, capped at the subtotal.
    'discount_type'  => 'percent',
    'discount_value' => 10,          // 10 => 10% off (or £10 off if 'fixed')

    // How long a coupon stays valid after it is emailed.
    'expires_in_days' => 60,

    // Codes look like FLAME-7K3QX9.
    'code_prefix' => 'FLAME',

    // Only count orders placed on/after this date (e.g. '2026-10-12').
    // null = count every completed order the customer has ever had, so
    // existing regulars are rewarded for their history at launch.
    'counting_starts_at' => null,
];