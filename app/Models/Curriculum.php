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
        'acad_year_id',
        'program_id',
        'program_major_id',
        'created_by',
        'updated_by',
    ];

    public function AcadYear()
    {
        return $this->belongsTo(AcadYear::class);
    }
    
    public function Program()
    {
        return $this->belongsTo(Program::class);
    }

    public function ProgramMajor()
    {
        return $this->belongsTo(ProgramMajor::class);
    }
}
