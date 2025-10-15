<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Author extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name', 'email', 'bio', 'main_title', 'preferred_social_network', 'preferred_social_network_username'];

    use SoftDeletes;

    /**
     * Get the posts from the Author.
     */
    public function post(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
