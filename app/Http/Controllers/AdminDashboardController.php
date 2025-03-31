<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $data = [];

        if ($user->role === 'hrd') {
            $data['totalKaryawan'] = User::count();
            $data['pending'] = Cuti::where('status_manager', 'approved')->where('status_hrd', 'pending')->count();
            $data['approved'] = Cuti::where('status_hrd', 'approved')->count();
            $data['rejected'] = Cuti::where('status_hrd', 'rejected')->count();
        } elseif ($user->role === 'manager') {
            $departmentQuery = function($query) use ($user) {
                $query->where('departemen_id', $user->departemen_id);
            };

            $data['pending'] = Cuti::whereHas('user', $departmentQuery)->where('status_manager', 'pending')->count();
            $data['approved'] = Cuti::whereHas('user', $departmentQuery)->where('status_manager', 'approved')->count();
            $data['rejected'] = Cuti::whereHas('user', $departmentQuery)->where('status_manager', 'rejected')->count();
        }

        $notification = null;
        if ($user && $user->tanggal_akhir_kerja) {
            try {
                $contractEndDate = Carbon::parse($user->tanggal_akhir_kerja);
                $daysUntilEnd = now()->diffInDays($contractEndDate);

                Log::info('Admin contract end calculation', [
                    'user_id' => $user->user_id,
                    'contract_end_date' => $contractEndDate->format('Y-m-d'),
                    'days_until_end' => $daysUntilEnd
                ]);

                if ($daysUntilEnd >= 0 && $daysUntilEnd <= 30 && $user->jumlah_cuti > 0) {
                    $notification = [
                        'type' => 'warning',
                        'message' => "Kontrak Anda akan berakhir dalam " . floor($daysUntilEnd) . " hari. Anda masih memiliki {$user->jumlah_cuti} hari cuti yang belum digunakan."
                    ];
                }
            } catch (\Exception $e) {
                Log::error('Error calculating admin contract end date', [
                    'error' => $e->getMessage()
                ]);
            }
        }

        $data['notification'] = $notification;
        $data['totalTerpakai'] = Cuti::where('user_id', $user->user_id)->where('status', 'Approved')->sum('jumlah');

        return view('admin.dashboard', $data);
    }
}
