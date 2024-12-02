<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campus extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CreatedUpdatedBy;

    protected $table = 'campuses';

    protected $fillable = [
        'campus_name',
        'isSatelliteCampus',
        'created_by',
        'updated_by',
    ];

    protected static function booted()
    {
        static::saving(function($campus){
            if($campus->isSatelliteCampus){
                $campus->campus_name = strtoupper($campus->campus_name);
            }
        });
    }

    public function College() {
        return $this->hasMany('College', 'campus_id');
    }

    public function Program() {
        return $this->hasMany('Program', 'program_id');
    }
    
}