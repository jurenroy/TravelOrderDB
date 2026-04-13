<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $primaryKey = 'employee_id';
    
    protected $fillable = [
        'employee_id',
        'name_id',
        'position_id',
        'division_id',
        'chief',
        'rd',
        'isActive',
        'bio_id',
        'sched_id',
        'salary_id'
    ];

    protected $table = 'employee';

    public $timestamps = false; // Disable timestamps

    // Relationship to Name model
    public function name()
    {
        return $this->belongsTo(Name::class, 'name_id', 'name_id');
    }

    // Accessor for fullname
    public function getFullnameAttribute()
    {
        if ($this->name) {
            return trim($this->name->first_name . ' ' . $this->name->middle_init . ' ' . $this->name->last_name);
        }
        return null;
    }

    // Include fullname automatically in JSON
    protected $appends = ['fullname'];
}
