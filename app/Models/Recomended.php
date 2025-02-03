<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recomended extends Model
{
    use HasFactory;
    protected $fillable = [
        'audit_id', 'title', 'status'
    ];

    public function audit()
    {
        return $this->belongsTo(Audit::class, 'audit_id');
    }

    public function getClosedFileSuratAttribute($value)
    {
        return $value ? url('storage/' . $value) : null;
    }
}
