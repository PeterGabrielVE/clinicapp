<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiagnosticAssignment extends Model
{
    use HasFactory;

    protected $table = 'diagnotic_assignments';
    protected $fillable = ['patient_id','diagnostic_id','observations','creation'];

}