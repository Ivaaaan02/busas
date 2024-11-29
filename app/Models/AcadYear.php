<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcadYear extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CreatedUpdatedBy;

    protected $table = 'acad_years';

    protected $fillable = [
        'year',
        'start_date',
        'end_date',
        'created_by',
        'updated_by',
    ];

    public function AcadTerm() {
        return $this->hasMany('AcadTerm', 'acad_year_id');
    }
}
