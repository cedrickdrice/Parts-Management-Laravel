<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamPart extends Model
{
    use HasFactory;

    /**
     * Table Name
     * @var string
     */
    protected $table = 'team_parts';

    protected $fillable = ['team_id', 'part_id', 'multiplier', 'static_price', 'team_price'];

    public static function calculateTeamPrice($listPrice, $multiplier, $staticPrice)
    {
        if ($multiplier) {
            return $listPrice * $multiplier;
        }
        return $staticPrice;
    }
}
