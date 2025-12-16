<?php

declare(strict_types=1);

test('returns top distributors with correct structure', function () {
    $response = $this->getJson('/api/top-distributors');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data' => [
                '*' => [
                    'rank',
                    'distributor_id',
                    'distributor_name',
                    'total_sales',
                ],
            ],
            'pagination' => [
                'current_page',
                'per_page',
                'total',
                'last_page',
            ],
        ]);
});

test('returns distributors within top 200 ranks', function () {
    $response = $this->getJson('/api/top-distributors?per_page=250');

    $response->assertStatus(200);

    $data = $response->json('data');

    // All distributors should have rank <= 200
    foreach ($data as $distributor) {
        expect($distributor['rank'])->toBeLessThanOrEqual(200);
    }

    // With DENSE_RANK, there may be more than 200 records due to ties
    // but all should have rank <= 200
    $maxRank = collect($data)->max('rank');
    expect($maxRank)->toBeLessThanOrEqual(200);
});

test('ranks distributors correctly with same sales having same rank', function () {
    $response = $this->getJson('/api/top-distributors?per_page=250');

    $response->assertStatus(200);

    $data = $response->json('data');

    $salesByRank = [];
    foreach ($data as $item) {
        if (isset($salesByRank[$item['rank']])) {
            expect($item['total_sales'])->toBe($salesByRank[$item['rank']]);
        } else {
            $salesByRank[$item['rank']] = $item['total_sales'];
        }
    }
});

test('calculates correct total sales for Demario Purdy', function () {
    $response = $this->getJson('/api/top-distributors?per_page=250');

    $response->assertStatus(200);

    $data = $response->json('data');
    $distributor = collect($data)->firstWhere('distributor_name', 'Demario Purdy');

    expect($distributor)->not->toBeNull();
    expect((float) $distributor['total_sales'])->toEqual(22026.75);
    expect($distributor['rank'])->toBe(1);
});

test('calculates correct total sales for Floy Miller', function () {
    $response = $this->getJson('/api/top-distributors?per_page=250');

    $response->assertStatus(200);

    $data = $response->json('data');
    $distributor = collect($data)->firstWhere('distributor_name', 'Floy Miller');

    expect($distributor)->not->toBeNull();
    expect((float) $distributor['total_sales'])->toEqual(9645.00);
});

test('calculates correct total sales for Loy Schamberger', function () {
    $response = $this->getJson('/api/top-distributors?per_page=250');

    $response->assertStatus(200);

    $data = $response->json('data');
    $distributor = collect($data)->firstWhere('distributor_name', 'Loy Schamberger');

    expect($distributor)->not->toBeNull();
    expect((float) $distributor['total_sales'])->toEqual(575.00);
});

test('rank 197 has Chaim Kuhn and Eliane Bogisich', function () {
    $response = $this->getJson('/api/top-distributors?per_page=250');

    $response->assertStatus(200);

    $data = $response->json('data');

    // Find Chaim Kuhn and Eliane Bogisich
    $chaimKuhn = collect($data)->firstWhere('distributor_name', 'Chaim Kuhn');
    $elianeBogisich = collect($data)->firstWhere('distributor_name', 'Eliane Bogisich');

    // Both should be at rank 197 with $360.00 sales
    expect($chaimKuhn)->not->toBeNull('Chaim Kuhn should be in top 200 ranks');
    expect($elianeBogisich)->not->toBeNull('Eliane Bogisich should be in top 200 ranks');
    expect($chaimKuhn['rank'])->toBe(197, 'Chaim Kuhn should be at rank 197');
    expect($elianeBogisich['rank'])->toBe(197, 'Eliane Bogisich should be at rank 197');
    expect((float) $chaimKuhn['total_sales'])->toEqual(360.00, 'Chaim Kuhn should have $360.00 sales');
    expect((float) $elianeBogisich['total_sales'])->toEqual(360.00, 'Eliane Bogisich should have $360.00 sales');
});

test('supports pagination', function () {
    $response = $this->getJson('/api/top-distributors?per_page=10&page=2');

    $response->assertStatus(200);

    $pagination = $response->json('pagination');

    expect($pagination['per_page'])->toBe(10);
    expect($pagination['current_page'])->toBe(2);
});

test('returns distributors sorted by total sales descending', function () {
    $response = $this->getJson('/api/top-distributors?per_page=50');

    $response->assertStatus(200);

    $data = $response->json('data');

    $previousSales = PHP_FLOAT_MAX;
    foreach ($data as $item) {
        expect($item['total_sales'])->toBeLessThanOrEqual($previousSales);
        $previousSales = $item['total_sales'];
    }
});
