<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\SoftDeletes;

class Program extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CreatedUpdatedBy; 

    protected $table = 'programs';

    protected $fillable = [
        'program_name',
        'program_abbreviation',
        'campus_id',
        'college_id',
        'created_by',
        'updated_by',
    ];

    public function Campus()
    {
        return $this->belongsTo(Campus::class);
    }
    
    public function College()
    {
        return $this->belongsTo(College::class);
    }


    public function ProgramMajor() {
        return $this->hasMany(ProgramMajor::class);
    }

    public function Course() {
        return $this->hasMany('Course', 'program_id');
    }
}