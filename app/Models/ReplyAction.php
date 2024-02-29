<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReplyAction extends Model
{
    public $fillable = [
        'post_id',
        'reply_id',
        'action_type',
        'user_id'
    ];
    use HasFactory;
}
