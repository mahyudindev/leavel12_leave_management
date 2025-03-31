<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departemen extends Model
{
    use HasFactory;

    protected $table = 'departemen';

    protected $primaryKey = 'departemen_id';

    protected $fillable = [
        'nama',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'departemen_id', 'departemen_id');
    }

    public static function searchByName($name)
    {
        return self::where('nama', 'like', "%{$name}%")->get();
    }
}