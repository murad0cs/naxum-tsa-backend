<?php

declare(strict_types=1);

test('returns commission report with correct structure', function () {
    $response = $this->getJson('/api/commission-report');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data' => [
                '*' => [
                    'order_id',
                    'invoice',
                    'purchaser',
                    'purchaser_id',
                    'distributor',
                    'distributor_id',
                    'referred_distributors',
                    'order_date',
                    'order_total',
                    'percentage',
                    'commission',
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

test('filters by distributor name', function () {
    $response = $this->getJson('/api/commission-report?distributor=Fisher');

    $response->assertStatus(200);

    $data = $response->json('data');

    foreach ($data as $item) {
        if ($item['distributor']) {
            expect($item['distributor'])->toContain('Fisher');
        }
    }
});

test('filters by date range', function () {
    $response = $this->getJson('/api/commission-report?date_from=2020-01-01&date_to=2020-01-31');

    $response->assertStatus(200);

    $data = $response->json('data');

    foreach ($data as $item) {
        expect($item['order_date'])->toBeGreaterThanOrEqual('2020-01-01');
        expect($item['order_date'])->toBeLessThanOrEqual('2020-01-31');
    }
});

test('filters by invoice number', function () {
    $response = $this->getJson('/api/commission-report?invoice=ABC4170');

    $response->assertStatus(200);

    $data = $response->json('data');

    expect(count($data))->toBeGreaterThanOrEqual(1);
    expect($data[0]['invoice'])->toBe('ABC4170');
});

test('calculates correct commission for ABC4170', function () {
    $response = $this->getJson('/api/commission-report?invoice=ABC4170');

    $response->assertStatus(200);

    $data = $response->json('data');
    $order = collect($data)->firstWhere('invoice', 'ABC4170');

    expect($order)->not->toBeNull();
    expect((float) $order['commission'])->toEqual(6.00);
});

test('calculates correct commission for ABC6931', function () {
    $response = $this->getJson('/api/commission-report?invoice=ABC6931');

    $response->assertStatus(200);

    $data = $response->json('data');
    $order = collect($data)->firstWhere('invoice', 'ABC6931');

    expect($order)->not->toBeNull();
    expect((float) $order['commission'])->toEqual(37.20);
});

test('calculates correct commission for ABC23352', function () {
    $response = $this->getJson('/api/commission-report?invoice=ABC23352');

    $response->assertStatus(200);

    $data = $response->json('data');
    $order = collect($data)->firstWhere('invoice', 'ABC23352');

    expect($order)->not->toBeNull();
    expect((float) $order['commission'])->toEqual(27.60);
});

test('calculates correct commission for ABC3010', function () {
    $response = $this->getJson('/api/commission-report?invoice=ABC3010');

    $response->assertStatus(200);

    $data = $response->json('data');
    $order = collect($data)->firstWhere('invoice', 'ABC3010');

    expect($order)->not->toBeNull();
    expect((float) $order['commission'])->toEqual(0.0);
});

test('calculates correct commission for ABC19323', function () {
    $response = $this->getJson('/api/commission-report?invoice=ABC19323');

    $response->assertStatus(200);

    $data = $response->json('data');
    $order = collect($data)->firstWhere('invoice', 'ABC19323');

    expect($order)->not->toBeNull();
    expect((float) $order['commission'])->toEqual(0.0);
});

test('returns order items for specific order', function () {
    $response = $this->getJson('/api/commission-report?invoice=ABC4170');
    $data = $response->json('data');
    $orderId = $data[0]['order_id'];

    $itemsResponse = $this->getJson("/api/commission-report/order/{$orderId}/items");

    $itemsResponse->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data' => [
                '*' => [
                    'sku',
                    'product_name',
                    'price',
                    'quantity',
                    'total',
                ],
            ],
        ]);
});

test('supports pagination', function () {
    $response = $this->getJson('/api/commission-report?per_page=5&page=1');

    $response->assertStatus(200);

    $data = $response->json('data');
    $pagination = $response->json('pagination');

    expect(count($data))->toBeLessThanOrEqual(5);
    expect($pagination['per_page'])->toBe(5);
    expect($pagination['current_page'])->toBe(1);
});
