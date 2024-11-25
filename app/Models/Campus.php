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
        'campus_address',
        'isSatelliteCampus',
        'created_by',
        'updated_by',
    ];

    public function Colleges() {
        return $this->hasMany('College', 'campus_id');
    }

    public function Program() {
        return $this->hasMany('Program', 'program_id');
    }
    
}