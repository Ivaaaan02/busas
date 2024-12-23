<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseStudentRecord extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CreatedUpdatedBy; 

    protected $table = 'course_student_records';

    protected $fillable = [
        'course_id',
        'student_record_id',
        'acad_term_id',
        'created_by',
        'updated_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function course(){
        return $this->belongsTo(Course::class, 'course_id');
    }
    public function studentRecord(){
        return $this->belongsTo(StudentRecord::class, 'student_record_id');
    }
    public function acadTerm(){
        return $this->belongsTo(AcadTerm::class, 'acad_term_id');
    }
}
