<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recomended extends Model
{
    use HasFactory;

    protected $table = 'recomendeds';
    protected $primaryKey = 'id';

    protected $fillable = ['audit_id', 'title', 'status', 'closed_file_surat', 'kesesuaian'];

    // Relasi ke model Audit (many-to-one)
    public function audit()
    {
        return $this->belongsTo(Audit::class, 'audit_id');
    }

    public function tindakLanjut()
    {
        return $this->hasMany(TindakLanjut::class, 'id_recomendeds', 'id');
    }
}
