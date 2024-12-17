<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\SoftDeletes;

class Curriculum extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CreatedUpdatedBy; 

    protected $table = 'curricula';

    protected $fillable = [
        'curriculum_name',
        'acad_term_id',
        'program_id',
        'program_major_id',
        'created_by',
        'updated_by',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function acadTerm()
    {
        return $this->belongsTo(AcadTerm::class);
    }
    
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function programMajor()
    {
        return $this->belongsTo(ProgramMajor::class);
    }

    public function course() {
        return $this->hasMany(Course::class);
    }
}
