<?php
namespace App\Exports;

use App\Models\User;
use App\Models\Departemen;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersExport implements FromCollection, WithHeadings, WithCustomStartCell, WithStyles, WithTitle, WithEvents
{
    protected $departemenId;
    protected $departemenNama;

    public function __construct($departemenId = null)
    {
        $this->departemenId = $departemenId;
        if ($departemenId) {
            $departemen = Departemen::find($departemenId);
            $this->departemenNama = $departemen ? $departemen->nama : 'Semua Departemen';
        } else {
            $this->departemenNama = 'Semua Departemen';
        }
    }

    public function collection()
    {
        $query = User::select('users.name', 'departemen.nama as departemen_nama', 'jabatan.nama as jabatan_nama', 'users.tanggal_akhir_kerja')
            ->leftJoin('departemen', 'users.departemen_id', '=', 'departemen.departemen_id')
            ->leftJoin('jabatan', 'users.jabatan_id', '=', 'jabatan.jabatan_id');

        if ($this->departemenId) {
            $query->where('users.departemen_id', $this->departemenId);
        }

        return $query->get()
            ->map(function ($user, $index) {
                return [
                    'No' => $index + 1,
                    'Nama' => $user->name,
                    'Departemen' => $user->departemen_nama ?? 'Tidak Ada',
                    'Jabatan' => $user->jabatan_nama ?? 'Tidak Ada',
                    'Status' => $user->tanggal_akhir_kerja ? 'Karyawan Kontrak' : 'Karyawan Tetap',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama',
            'Departemen',
            'Jabatan',
            'Status',
        ];
    }

    public function startCell(): string
    {
        return 'A6';
    }

    public function title(): string
    {
        return 'Data Karyawan - ' . $this->departemenNama;
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastColumn = 'E';

        // Title
        $sheet->mergeCells('A1:' . $lastColumn . '1');
        $sheet->setCellValue('A1', 'DATA KARYAWAN');
        $sheet->mergeCells('A2:' . $lastColumn . '2');
        $sheet->setCellValue('A2', 'Departemen: ' . $this->departemenNama);
        $sheet->mergeCells('A3:' . $lastColumn . '3');
        $sheet->setCellValue('A3', 'Tanggal Export: ' . now()->format('d/m/Y'));

        // Title style
        $sheet->getStyle('A1:' . $lastColumn . '3')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Headers style
        $sheet->getStyle('A6:' . $lastColumn . '6')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4A90E2'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Data cells style
        $sheet->getStyle('A7:' . $lastColumn . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(5);  // No
        $sheet->getColumnDimension('B')->setWidth(30); // Nama
        $sheet->getColumnDimension('C')->setWidth(20); // Departemen
        $sheet->getColumnDimension('D')->setWidth(20); // Jabatan
        $sheet->getColumnDimension('E')->setWidth(20); // Status

        return $sheet;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getStyle('A6:' . $event->sheet->getHighestColumn() . $event->sheet->getHighestRow())
                    ->getAlignment()->setWrapText(true);
            },
        ];
    }
}
