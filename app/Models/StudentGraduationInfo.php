<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentGraduationInfo extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CreatedUpdatedBy; 

    protected $table = 'student_graduation_infos';

    protected $fillable = [
        'graduation_date',
        'degree_attained',
        'date_attended',
        'board_approval',
        'latin_honor',
        'gwa',
        'student_id',
        'created_by',
        'updated_by',
    ];

    public function Student(){
        return $this->belongsTo(Student::class, 'student_id');
    }
}
