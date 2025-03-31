<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cuti;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $totalTerpakai = Cuti::where('user_id', $user->user_id)
            ->where('status', 'Approved')
            ->sum('jumlah');
            
        $jumlahCuti = $user ? $user->jumlah_cuti : 'Data tidak tersedia';
        $notification = null;

        if ($user && $user->tanggal_akhir_kerja) {
            try {
                $contractEndDate = Carbon::parse($user->tanggal_akhir_kerja);
                $daysUntilEnd = now()->diffInDays($contractEndDate);
                
                // Debug
                // Log::info('Contract end calculation', [
                //     'contract_end_date' => $contractEndDate->format('Y-m-d'),
                //     'days_until_end' => $daysUntilEnd
                // ]);

                // Show notification for 30 days before contract ends
                if ($daysUntilEnd >= 0 && $daysUntilEnd <= 30) {
                    if ($user->jumlah_cuti > 0) {
                        $notification = [
                            'type' => 'warning',
                            'message' => "Kontrak Anda akan berakhir dalam " . floor($daysUntilEnd) . " hari. Anda masih memiliki {$user->jumlah_cuti} hari cuti yang belum digunakan."
                        ];
                        
                        // Debug logging
                        Log::info('Notification triggered', [
                            'days_until_end' => floor($daysUntilEnd),
                            'total_cuti' => $user->jumlah_cuti
                        ]);
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error calculating contract end date', [
                    'error' => $e->getMessage()
                ]);
            }
        }
    
        return view('users.dashboard', compact('jumlahCuti', 'totalTerpakai', 'notification'));
    }
}