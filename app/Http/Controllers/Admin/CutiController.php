<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Cuti;
use App\Models\JenisCuti;
use Carbon\Carbon;

class CutiController extends Controller
{
    public function create()
    {
        $jenisCuti = JenisCuti::all();
        return view('admin.cuti.create', ['jenisCuti' => $jenisCuti]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_cuti' => 'required|exists:jenis_cuti,id',
            'tanggal_awal' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_awal',
        ]);

        $tanggalAwal = Carbon::parse($request->tanggal_awal);
        $tanggalAkhir = Carbon::parse($request->tanggal_akhir);
        $jumlahHari = $tanggalAwal->diffInDays($tanggalAkhir) + 1;

        $user = Auth::user();

        if ($jumlahHari > $user->jumlah_cuti) {
            return back()->with('error', 'Jumlah hari cuti melebihi sisa cuti Anda.');
        }

        $user->jumlah_cuti -= $jumlahHari;
        $user->save();

        Cuti::create([
            'id_user' => $user->id,
            'jenis_cuti' => $request->jenis_cuti,
            'tanggal_awal' => $tanggalAwal,
            'tanggal_akhir' => $tanggalAkhir,
            'jumlah' => $jumlahHari,
            'status' => 'waiting',
            'status_manager' => 'approved',
            'status_hrd' => 'pending'
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Pengajuan cuti berhasil dikirim ke HRD.');
    }

    public function adminCuti(Request $request, $status = 'pending')
    {
        $query = Cuti::with(['user', 'jenisCuti', 'user.departemen'])
            ->when($status !== 'all', function ($query) use ($status) {
                $query->where(function ($query) use ($status) {
                    if (auth()->user()->role === 'manager') {
                        $query->where('status_manager', $status)
                            ->whereHas('user', function ($query) {
                                $query->where('departemen_id', auth()->user()->departemen_id);
                            });
                    } else {
                        $query->where('status_manager', 'approved')
                            ->where('status_hrd', $status);
                    }
                });
            });

        return view('admin.cuti.index', [
            'daftarCuti' => $query->orderBy('created_at', 'desc')->paginate(10),
            'status' => $status
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $cuti = Cuti::findOrFail($id);
        $user = $cuti->user;

        if ($request->action === 'approve') {
            if (auth()->user()->role === 'manager') {
                $cuti->status_manager = 'approved';
                $cuti->approved_at_manager = now();
            } else {
                $cuti->status_hrd = 'approved';
                $cuti->approved_at_hrd = now();
                $cuti->status = 'approved';
            }
        } else {
            $user->jumlah_cuti += $cuti->jumlah;
            $user->save();

            if (auth()->user()->role === 'manager') {
                $cuti->status_manager = 'rejected';
                $cuti->status = 'rejected';
                $cuti->notes_manager = $request->notes;
            } else {
                $cuti->status_hrd = 'rejected';
                $cuti->status = 'rejected';
                $cuti->notes_hrd = $request->notes;
            }
        }

        $cuti->save();

        return redirect()->back()->with('success', 'Status cuti berhasil diperbarui.');
    }
}
