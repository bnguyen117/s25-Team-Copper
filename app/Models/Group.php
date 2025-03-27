<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'creator_id', 'is_private'];

    public function members()
    {
        return $this->belongsToMany(User::class, 'group_members');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
