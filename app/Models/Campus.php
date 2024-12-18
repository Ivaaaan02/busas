<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Campus extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CreatedUpdatedBy;

    protected $table = 'campuses';

    protected $fillable = [
        'campus_name',
        'campus_address',
        'isSatelliteCampus',
        'created_by',
        'updated_by',
    ];

    protected static function booted()
    {
        static::saving(function($campus){
            if($campus->isSatelliteCampus){
                $campus->campus_name = strtoupper($campus->campus_name);
                DB::table('colleges')
                ->where('campus_id', $campus->id)
                ->update(['college_name' => $campus->campus_name]);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function College() {
        return $this->hasMany(College::class);
    }

    public function Program() {
        return $this->hasMany('Program', 'program_id');
    } 
}