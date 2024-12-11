<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CreatedUpdatedBy; 

    protected $table = 'courses';

    protected $fillable = [
        'descriptive_title',
        'course_code',
        'course_unit',
        'curriculum_id',
        'created_by',
        'updated_by',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function AcadTerm()
    {
        return $this->belongsTo(AcadTerm::class);
    }
    public function Program()
    {
        return $this->belongsTo(Program::class);
    }

    public function ProgramMajor()
    {
        return $this->belongsTo(ProgramMajor::class);
    }

    public function Curriculum()
    {
        return $this->belongsTo(Curriculum::class);
    }
}
