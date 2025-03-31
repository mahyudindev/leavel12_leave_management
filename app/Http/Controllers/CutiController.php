<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Cuti;
use App\Models\JenisCuti;
use Carbon\Carbon;

class CutiController extends Controller
{
    public function riwayatCuti(Request $request)
    {
        $query = Auth::user()->cuti()->with('jenisCuti');

        if ($request->has('sort') && in_array($request->sort, ['tanggal_awal', 'tanggal_akhir'])) {
            $query->orderBy($request->sort, 'asc');
        }

        return view('users.history', [
            'riwayatCuti' => $query->paginate(10)
        ]);
    }

    public function pengajuanCuti()
    {
        $jenisCuti = JenisCuti::all();
        return view('users.pengajuan', ['jenisCuti' => $jenisCuti]);
    }

    public function ajukanCuti(Request $request)
    {
        $request->validate([
            'jenis_cuti' => 'required|exists:jenis_cuti,jenis_cuti_id',
            'tanggal_awal' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_awal',
        ]);

        $tanggalAwal = Carbon::parse($request->tanggal_awal)->format('Y-m-d');
        $tanggalAkhir = Carbon::parse($request->tanggal_akhir)->format('Y-m-d');
        $jumlahHari = Carbon::parse($request->tanggal_awal)->diffInDays($request->tanggal_akhir) + 1;

        $user = Auth::user();

        if ($jumlahHari > $user->jumlah_cuti) {
            return back()->with('error', 'Jumlah hari cuti melebihi sisa cuti Anda.');
        }

        $status = 'Pending';
        $statusManager = 'Pending';
        $statusHrd = 'Pending';
        $approvedAtManager = null;

        if ($user->role === 'manager') {
            $statusManager = 'Approved';
            $approvedAtManager = now();
        }

        try {
            $cuti = Cuti::create([
                'user_id' => $user->user_id,
                'jenis_cuti_id' => $request->jenis_cuti,
                'tanggal_awal' => $tanggalAwal,
                'tanggal_akhir' => $tanggalAkhir,
                'jumlah' => $jumlahHari,
                'status' => $status,
                'status_manager' => $statusManager,
                'status_hrd' => $statusHrd,
                'approved_at_manager' => $approvedAtManager
            ]);

            if ($cuti) {
                $user->jumlah_cuti -= $jumlahHari;
                $user->save();

                $message = $user->role === 'manager' 
                    ? 'Pengajuan cuti berhasil dikirim ke HRD.' 
                    : 'Pengajuan cuti berhasil dikirim. Cek History Untuk Lihat Hasil.';

                return redirect()->back()->with('success', $message);
            }
            
            return back()->with('error', 'Gagal menyimpan pengajuan cuti.');
        } catch (\Exception $e) {
            \Log::error('Error submitting leave request: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function cancel(Request $request, $id)
    {
        $cuti = Cuti::findOrFail($id);

        if (auth()->user()->role === 'manager') {
            if ($cuti->status !== 'Pending' || 
                $cuti->status_manager !== 'Approved' || 
                $cuti->status_hrd !== 'Pending' || 
                $cuti->user->departemen_id !== auth()->user()->departemen_id) {
                return back()->with('error', 'Anda tidak memiliki izin untuk membatalkan pengajuan ini.');
            }
        } else {
            if ($cuti->status !== 'Pending' || 
                $cuti->status_manager !== 'Pending' || 
                $cuti->status_hrd !== 'Pending' || 
                $cuti->user_id !== Auth::id()) {
                return back()->with('error', 'Pengajuan cuti ini sudah tidak dapat dibatalkan.');
            }
        }

        $user = $cuti->user;
        $user->jumlah_cuti += $cuti->jumlah;
        $user->save();

        $cuti->delete();

        return back()->with('success', 'Pengajuan cuti berhasil dibatalkan.');
    }
}
