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

        $riwayatCuti = $query->paginate(10);

        return view('users.history', compact('riwayatCuti'));
    }

    public function pengajuanCuti()
    {
        $jenisCuti = JenisCuti::all();
        return view('users.pengajuan', [
            'jenisCuti' => $jenisCuti
        ]);
    }

    public function ajukanCuti(Request $request)
    {
        // Validasi input
        $request->validate([
            'jenis_cuti' => 'required|exists:jenis_cuti,jenis_cuti_id',
            'tanggal_awal' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_awal',
        ]);

        // Parse tanggal untuk perhitungan jumlah hari
        $tanggalAwalCarbon = Carbon::parse($request->tanggal_awal);
        $tanggalAkhirCarbon = Carbon::parse($request->tanggal_akhir);
        
        // Format tanggal untuk penyimpanan dalam database
        $tanggalAwal = $tanggalAwalCarbon->format('Y-m-d');
        $tanggalAkhir = $tanggalAkhirCarbon->format('Y-m-d');
        
        // Menghitung jumlah hari cuti
        $jumlahHari = $tanggalAwalCarbon->diffInDays($tanggalAkhirCarbon) + 1;

        $user = Auth::user();

        // Memeriksa apakah jumlah hari cuti melebihi sisa cuti
        if ($jumlahHari > $user->jumlah_cuti) {
            return back()->with('error', 'Jumlah hari cuti melebihi sisa cuti Anda.');
        }

        $status = 'Pending';
        $statusManager = 'Pending';
        $statusHrd = 'Pending';

        // Jika role user adalah manager, status manager langsung diset 'Approved'
        if ($user->role === 'manager') {
            $statusManager = 'Approved';
            $approvedAtManager = now();
        } else {
            $approvedAtManager = null;
        }

        try {
            // Menyimpan pengajuan cuti
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

            // Jika pengajuan cuti berhasil
            if ($cuti) {
                $user->jumlah_cuti -= $jumlahHari;
                $user->save();

                $message = $user->role === 'manager' 
                    ? 'Pengajuan cuti berhasil dikirim ke HRD.' 
                    : 'Pengajuan cuti berhasil dikirim. Cek History Untuk Lihat Hasil.';

                return redirect()->back()->with('success', $message);
            }
            
            // Jika gagal
            return back()->with('error', 'Gagal menyimpan pengajuan cuti.');
        } catch (\Exception $e) {
            \Log::error('Error submitting leave request: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function adminCuti($status = 'Pending')
    {
        $statusProperCase = ucfirst(strtolower($status));
        
        $user = auth()->user();
        $query = Cuti::with([
            'user' => function ($query) {
                $query->with('departemen');
            },
            'jenisCuti'
        ]);

        if ($user->role === 'manager') {
            $query->whereHas('user', function($q) use ($user) {
                $q->where('departemen_id', $user->departemen_id);
            });
            
            if ($statusProperCase === 'Pending') {
                $query->where('status_manager', 'Pending');
            } elseif ($statusProperCase === 'Approved') {
                $query->where('status_manager', 'Approved');
            } elseif ($statusProperCase === 'Rejected') {
                $query->where('status_manager', 'Rejected');
            }
        } elseif ($user->role === 'hrd') {
            if ($statusProperCase === 'Pending') {
                $query->where('status_manager', 'Approved')
                      ->where('status_hrd', 'Pending');
            } elseif ($statusProperCase === 'Approved') {
                $query->where('status_hrd', 'Approved');
            } elseif ($statusProperCase === 'Rejected') {
                $query->where('status_hrd', 'Rejected');
            }
        }

        $daftarCuti = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return view('admin.cuti.index', compact('daftarCuti', 'status'));
    }

    public function updateCuti(Request $request, $cuti_id)
    {
        $cuti = Cuti::findOrFail($cuti_id);
        $action = $request->action;
        $notes = $request->input('notes', null);
        $user = auth()->user();
        $now = now();

        if ($action === 'reject' && !$notes) {
            return redirect()->back()->with('error', 'Catatan harus diisi jika pengajuan ditolak.');
        }

        $employee = $cuti->user;

        if ($action === 'approve') {
            if ($user->role === 'manager' && $cuti->status_manager === 'Pending') {
                $cuti->status_manager = 'Approved';
                $cuti->approved_at_manager = $now;
                $cuti->notes_manager = null;
            } elseif ($user->role === 'hrd' && $cuti->status_manager === 'Approved' && $cuti->status_hrd === 'Pending') {
                $cuti->status_hrd = 'Approved';
                $cuti->approved_at_hrd = $now;
                $cuti->notes_hrd = null;
            }
        } elseif ($action === 'reject') {
            $employee->jumlah_cuti += $cuti->jumlah;
            $employee->save();
            
            if ($user->role === 'manager') {
                $cuti->status_manager = 'Rejected';
                $cuti->notes_manager = $notes;
            } elseif ($user->role === 'hrd') {
                $cuti->status_hrd = 'Rejected';
                $cuti->notes_hrd = $notes;
            }
        }

        if ($cuti->status_manager === 'Rejected' || $cuti->status_hrd === 'Rejected') {
            $cuti->status = 'Rejected';
        } elseif ($cuti->status_manager === 'Approved' && $cuti->status_hrd === 'Approved') {
            $cuti->status = 'Approved';
        } else {
            $cuti->status = 'Pending';
        }

        $cuti->save();

        return redirect()->back()->with('success', "Cuti berhasil di-{$action}.");
    }
}
