<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Cuti extends Model
{
    use HasFactory;

    protected $table = 'cuti';

    protected $primaryKey = 'cuti_id';

    protected $fillable = [
        'user_id',
        'tanggal_awal',
        'tanggal_akhir',
        'jumlah',
        'jenis_cuti_id',
        'status',
        'status_manager',
        'status_hrd',
        'approved_at_manager',
        'approved_at_hrd',
        'notes_manager',
        'notes_hrd',
    ];

    protected $casts = [
        'tanggal_awal' => 'date',
        'tanggal_akhir' => 'date',
        'approved_at_manager' => 'datetime',
        'approved_at_hrd' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function jenisCuti()
    {
        return $this->belongsTo(JenisCuti::class, 'jenis_cuti_id', 'jenis_cuti_id');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'departemen_id', 'departemen_id')
            ->whereHas('user', function ($query) {
                $query->where('user_id', $this->user_id);
            });
    }

    public static function filterByStatus($status)
    {
        return self::where('status', $status)->get();
    }

    public static function filterByStatusManager($status)
    {
        return self::where('status_manager', $status)->get();
    }

    public static function filterByStatusHRD($status)
    {
        return self::where('status_hrd', $status)->get();
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeStatusManager($query, $status)
    {
        return $query->where('status_manager', $status);
    }

    public function scopeStatusHRD($query, $status)
    {
        return $query->where('status_hrd', $status);
    }

    public function getTanggalAwalFormattedAttribute()
    {
        return Carbon::parse($this->tanggal_awal)->format('d/m/Y');
    }

    public function getTanggalAkhirFormattedAttribute()
    {
        return Carbon::parse($this->tanggal_akhir)->format('d/m/Y');
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'Pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
            'Approved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
            'Rejected' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
        ];

        return $badges[$this->status] ?? '';
    }

    public function getStatusManagerBadgeAttribute()
    {
        $badges = [
            'Pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
            'Approved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
            'Rejected' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
        ];

        return $badges[$this->status_manager] ?? '';
    }

    public function getStatusHRDBadgeAttribute()
    {
        $badges = [
            'Pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
            'Approved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
            'Rejected' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
        ];

        return $badges[$this->status_hrd] ?? '';
    }
}
