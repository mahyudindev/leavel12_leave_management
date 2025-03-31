<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisCuti extends Model
{
    use HasFactory;

    protected $table = 'jenis_cuti';

    protected $primaryKey = 'jenis_cuti_id';

    protected $fillable = [
        'nama_cuti',
    ];

    public function cuti()
    {
        return $this->hasMany(Cuti::class, 'jenis_cuti_id', 'jenis_cuti_id');
    }
}
