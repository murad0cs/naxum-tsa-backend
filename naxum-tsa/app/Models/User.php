<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{
    protected $table = 'users';

    public $timestamps = false;

    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'referred_by',
        'enrolled_date',
    ];

    protected $casts = [
        'enrolled_date' => 'date',
    ];

    /**
     * Get the user who referred this user.
     */
    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    /**
     * Get all users referred by this user.
     */
    public function referrals(): HasMany
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    /**
     * Get the categories this user belongs to.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'user_category', 'user_id', 'category_id');
    }

    /**
     * Get orders made by this user.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'purchaser_id');
    }

    /**
     * Check if the user is a distributor.
     */
    public function isDistributor(): bool
    {
        return $this->categories()->where('name', 'Distributor')->exists();
    }

    /**
     * Check if the user is a customer.
     */
    public function isCustomer(): bool
    {
        return $this->categories()->where('name', 'Customer')->exists();
    }

    /**
     * Get the full name of the user.
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}
