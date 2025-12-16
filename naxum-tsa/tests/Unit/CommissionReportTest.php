<?php

declare(strict_types=1);

use App\Repositories\CommissionReportRepository;

beforeEach(function () {
    $this->repository = app(CommissionReportRepository::class);
});

// Commission percentage tests based on referred distributors count
// 0-4 referred distributors = 5%
// 5-10 referred distributors = 10%
// 11-20 referred distributors = 15%
// 21-29 referred distributors = 20%
// 30+ referred distributors = 30%

test('returns 5 percent for 0 referred distributors', function () {
    expect($this->repository->getCommissionPercentage(0))->toBe(5);
});

test('returns 5 percent for 2 referred distributors', function () {
    expect($this->repository->getCommissionPercentage(2))->toBe(5);
});

test('returns 5 percent for 4 referred distributors', function () {
    expect($this->repository->getCommissionPercentage(4))->toBe(5);
});

test('returns 10 percent for 5 referred distributors', function () {
    expect($this->repository->getCommissionPercentage(5))->toBe(10);
});

test('returns 10 percent for 7 referred distributors', function () {
    expect($this->repository->getCommissionPercentage(7))->toBe(10);
});

test('returns 10 percent for 10 referred distributors', function () {
    expect($this->repository->getCommissionPercentage(10))->toBe(10);
});

test('returns 15 percent for 11 referred distributors', function () {
    expect($this->repository->getCommissionPercentage(11))->toBe(15);
});

test('returns 15 percent for 15 referred distributors', function () {
    expect($this->repository->getCommissionPercentage(15))->toBe(15);
});

test('returns 15 percent for 20 referred distributors', function () {
    expect($this->repository->getCommissionPercentage(20))->toBe(15);
});

test('returns 20 percent for 21 referred distributors', function () {
    expect($this->repository->getCommissionPercentage(21))->toBe(20);
});

test('returns 20 percent for 25 referred distributors', function () {
    expect($this->repository->getCommissionPercentage(25))->toBe(20);
});

test('returns 20 percent for 29 referred distributors', function () {
    expect($this->repository->getCommissionPercentage(29))->toBe(20);
});

test('returns 30 percent for 30 referred distributors', function () {
    expect($this->repository->getCommissionPercentage(30))->toBe(30);
});

test('returns 30 percent for 50 referred distributors', function () {
    expect($this->repository->getCommissionPercentage(50))->toBe(30);
});

test('returns 30 percent for 100 referred distributors', function () {
    expect($this->repository->getCommissionPercentage(100))->toBe(30);
});
