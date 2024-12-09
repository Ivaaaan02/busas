<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CreatedUpdatedBy; 

    protected $table = 'students';

    protected $fillable = [
        'last_name',
        'first_name',
        'middle_name',
        'suffix',
        'program_id',
        'sex',
        'address',
        'place_of_birth',
        'date_of_birth',
        'created_by',
        'updated_by',
    ];

    public function StudentRegistrationInfo() {
        return $this->hasMany(StudentRegistrationInfo::class);
    }

    public function StudentGraduationInfo() {
        return $this->hasMany(StudentGraduationInfo::class);
    }

    public function Program(){
        return $this->belongsTo(Program::class, 'program_id');
    }
}
