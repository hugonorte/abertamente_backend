<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

/**
 * @property mixed $title
 * @property mixed $tldr
 * @property mixed $content
 * @property mixed $image_path
 * @property mixed $author_id
 * @property mixed $category_id
 * @property mixed $published_at
 * @property mixed $status
 * @property mixed $id
 * @method static findOrFail(string $id)
 * @method first()
 */
class Post extends Model
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['title', 'email', 'bio', 'main_title', 'preferred_social_network', 'preferred_social_network_username'];

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
