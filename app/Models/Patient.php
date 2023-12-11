<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $table = 'patients';
    protected $fillable = ['document','first_name','last_name','birth_date','email','phone','genre'];

    public function assignments()
    {
        return $this->hasMany(DiagnosticAssignment::class);
    }

    public function diagnostics()
    {
        return $this->belongsToMany(
            Diagnostic::class,
            'diagnotic_assignments',
            'patient_id',
            'diagnostic_id'
        );
    }

}
