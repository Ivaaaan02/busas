<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcadTerm extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CreatedUpdatedBy;

    protected $table = 'acad_terms';

    protected $fillable = [
        'acad_term',
        'acad_year_id',
        'created_by',
        'updated_by',
    ];

    public function AcadYear()
    {
        return $this->belongsTo(AcadYear::class);
    }
}

