<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cuti;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanCutiExport;

class LaporanCutiController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->input('bulan', now()->format('Y-m'));

        $laporan = Cuti::with('user')
            ->where('status', 'Approved')
            ->whereYear('tanggal_awal', date('Y', strtotime($bulan)))
            ->whereMonth('tanggal_awal', date('m', strtotime($bulan)))
            ->get();

        return view('admin.laporan-cuti', compact('laporan', 'bulan'));
    }

    public function export(Request $request)
    {
        $bulan = $request->input('bulan', now()->format('Y-m'));
        return Excel::download(new LaporanCutiExport($bulan), 'laporan_cuti.xlsx');
    }
}
