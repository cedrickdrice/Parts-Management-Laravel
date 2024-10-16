<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Table Name
     * @var string
     */
    protected $table = 'users';

    protected $fillable = [
        'name', 'email', 'password', 'role',
    ];

    protected $with = ['teams'];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'user_team', 'user_id', 'team_id');
    }
}
