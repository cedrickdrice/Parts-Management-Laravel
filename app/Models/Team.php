<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    /**
     * Table Name
     * @var string
     */
    protected $table = 'teams';

    protected $fillable = ['name'];

    public function members()
    {
        return $this->belongsToMany(User::class, 'user_team', 'team_id', 'user_id');
    }

    public function parts()
    {
        return $this->belongsToMany(Part::class, 'team_parts', 'team_id', 'part_id')
            ->withPivot('multiplier', 'static_price', 'team_price');
    }
}
