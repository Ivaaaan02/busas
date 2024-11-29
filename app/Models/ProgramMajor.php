<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgramMajor extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CreatedUpdatedBy; 

    protected $table = 'program_majors';

    protected $fillable = [
        'program_major_name',
        'program_major_abbreviation',
        'program_id',
        'created_by',
        'updated_by',
    ];

    public function Program(){
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function Course() {
        return $this->hasMany('Course', 'program_major_id');
    }
}