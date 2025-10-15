<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name'];

    use SoftDeletes;

    /**
     * Get the posts from this Category.
     */
    public function post(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
