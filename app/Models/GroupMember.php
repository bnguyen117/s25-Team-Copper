<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupMember extends Model
{
    use HasFactory;
    protected $fillable = [
        'group_id',
        'user_id',
        'role',
        'status',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
