<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\AcadTerm;

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

    protected static function booted()
    {
        static::created(function ($AcadYear) {
            $years = explode(' - ', $AcadYear->year);
            $startYear = $years[0];
            $endYear = $years[1];

            AcadTerm::create([
                'acad_year_id' => $AcadYear->id,
                'acad_term' => "1st Semester $startYear - $endYear",
            ]);
            AcadTerm::create([
                'acad_year_id' => $AcadYear->id,
                'acad_term' => "2nd Semester $startYear - $endYear",
            ]);
            AcadTerm::create([
                'acad_year_id' => $AcadYear->id,
                'acad_term' => "Summer $endYear",
            ]);
            AcadTerm::create([
                'acad_year_id' => $AcadYear->id,
                'acad_term' => "Mid Year $endYear",
            ]);
        });
    }

    public function AcadTerm() {
        return $this->hasMany(AcadTerm::class, 'acad_year_id');
    }
}
