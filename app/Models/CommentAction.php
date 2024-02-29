<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentAction extends Model
{
    public $fillable = [
        'post_id',
        'parent_comment_id',
        'action_type',
        'user_id'
    ];
    use HasFactory;
}
