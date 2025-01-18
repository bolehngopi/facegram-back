<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory;

    protected $fillable = [
        'caption',
        'post_id'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function attachments() {
        return $this->hasMany(PostAttachment::class);
    }

    public function casts() {
        return [
            'created_at' => 'datetime'
        ];
    }
}
