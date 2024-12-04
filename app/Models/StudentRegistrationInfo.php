<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentRegistrationInfo extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CreatedUpdatedBy; 

    protected $table = 'student_registration_infos';

    protected $fillable = [
        'last_date_attended',
        'category',
        'last_school_attended',
        'date_semester_admitted',
        'student_id',
        'created_by',
        'updated_by',
    ];

    public function Student(){
        return $this->belongsTo(Student::class, 'student_id');
    }
}
