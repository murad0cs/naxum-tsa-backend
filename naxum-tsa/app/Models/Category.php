<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    protected $table = 'categories';

    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    /**
     * Get the users that belong to this category.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_category', 'category_id', 'user_id');
    }
}



