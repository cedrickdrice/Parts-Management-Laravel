<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Part extends Model
{
    use HasFactory;

    /**
     * Table Name
     * @var string
     */
    protected $table = 'parts';

    protected $fillable = [
        'active', 'part_type', 'manufacturer', 'model_number', 'list_price',
    ];

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_parts', 'part_id', 'team_id')
            ->withPivot('multiplier', 'static_price', 'team_price');
    }
}
