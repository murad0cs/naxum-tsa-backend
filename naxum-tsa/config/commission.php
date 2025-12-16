<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Commission Percentage Tiers
    |--------------------------------------------------------------------------
    |
    | Define the commission percentage tiers based on the number of
    | distributors referred by a distributor at the time of order.
    |
    | Each tier has:
    | - min: Minimum number of referred distributors (inclusive)
    | - max: Maximum number of referred distributors (inclusive)
    | - percentage: Commission percentage for this tier
    |
    */
    'tiers' => [
        ['min' => 0, 'max' => 4, 'percentage' => 5],
        ['min' => 5, 'max' => 10, 'percentage' => 10],
        ['min' => 11, 'max' => 20, 'percentage' => 15],
        ['min' => 21, 'max' => 29, 'percentage' => 20],
        ['min' => 30, 'max' => PHP_INT_MAX, 'percentage' => 30],
    ],

    /*
    |--------------------------------------------------------------------------
    | Category IDs
    |--------------------------------------------------------------------------
    |
    | Define the category IDs for user types in the database.
    |
    */
    'categories' => [
        'distributor' => 1,
        'customer' => 2,
    ],

    /*
    |--------------------------------------------------------------------------
    | Top Distributors Limit
    |--------------------------------------------------------------------------
    |
    | Maximum number of distributors to show in the top distributors report.
    |
    */
    'top_distributors_limit' => 200,

    /*
    |--------------------------------------------------------------------------
    | Default Pagination
    |--------------------------------------------------------------------------
    |
    | Default number of items per page for paginated results.
    |
    */
    'default_per_page' => 10,
    'max_per_page' => 250,  // Allow extra for ties at rank boundaries
];
