<?php

namespace App\Exports;

use App\Models\Harvest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class HarvestsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return Harvest::with('crop', 'unit')->latest()->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanaman',
            'Tanggal Panen',
            'Kuantitas',
            'Satuan',
            'Harga Jual',
            'Total Nilai',
            'Catatan',
        ];
    }

    public function map($harvest): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $harvest->crop->name ?? '-',
            \Carbon\Carbon::parse($harvest->harvested_at)->format('d M Y'),
            $harvest->quantity,
            $harvest->unit->symbol ?? '-',
            'Rp ' . number_format($harvest->selling_price, 0, ',', '.'),
            'Rp ' . number_format($harvest->total_value, 0, ',', '.'),
            $harvest->notes ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '22C55E'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}