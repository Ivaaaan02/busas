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

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function campus()
    {
        return $this->hasOneThrough(Campus::class, Program::class, 'id', 'id', 'program_id', 'campus_id');
    }

    public function course()
    {
        return $this->hasOneThrough(Course::class, Curriculum::class, 'id', 'id', 'course_id', 'curriculum_id');
    }

    public function StudentRegistrationInfo() {
        return $this->hasOne(StudentRegistrationInfo::class);
    }

    public function StudentGraduationInfo() {
        return $this->hasMany(StudentGraduationInfo::class);
    }

    public function studentRecord() {
        return $this->hasMany(StudentRecord::class);
    }

    public function Program(){
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->middle_name . ' ' .  $this->last_name . ' ' . $this->suffix;
    }
}
