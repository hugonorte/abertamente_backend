<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name', 'email', 'bio', 'main_title', 'preferred_social_network', 'preferred_social_network_username'];

    use SoftDeletes;

    /**
     * Get the post that owns the comment.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Get the post that owns the comment.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Get the comments for the blog post.
     */
    public function bibliographic_reference(): HasMany
    {
        return $this->hasMany(BibliographicReference::class);
    }

    /**
     * Get the comments for the blog post.
     */
    public function footnote(): HasMany
    {
        return $this->hasMany(Footnote::class);
    }
}
