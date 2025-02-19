<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'display_name',
        'email',
        'birthdate',
        'avatar',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Set a one to many relationship
     * 
     * Where one user can have many debts.
     */
    public function debts()
    {
        return $this->hasMany(Debt::class);
    }

    /**
     * Set a one to many relationship
     * 
     * Where one user can have many Financial Goals.
     */
    public function financialGoals()
    {
        return $this->hasMany(FinancialGoal::class);
    }
/**
     * Set a one to many relationship
     * 
     * Where one user can have many Financial Goals.
     */
    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }


    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_members');
    }

    public function sentFriendRequests()
    {
        return $this->hasMany(FriendRequest::class, 'sender_id');
    }

    public function receivedFriendRequests()
    {
        return $this->hasMany(FriendRequest::class, 'receiver_id');
    }

    public function friends()
    {
        return $this->belongsToMany(User::class, 'friends', 'user_id', 'friend_id');
    }
}
