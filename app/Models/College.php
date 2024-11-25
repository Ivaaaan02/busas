<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\SoftDeletes;

class College extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CreatedUpdatedBy;

    protected $table = 'colleges';

    protected $fillable = [
        'college_name',
        'campus_id',
        'created_by',
        'updated_by',
    ];

    public function Campus()
    {
        return $this->belongsTo(Campus::class);
    }
    public function Program() {
        return $this->hasMany('Program', 'program_id');
    }
}
