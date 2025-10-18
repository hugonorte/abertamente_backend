<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

/**
 * @property mixed $name
 * @method static findOrFail(string $id)
 */
class Category extends Model
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;
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
