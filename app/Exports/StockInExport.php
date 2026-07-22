<?php

namespace App\Exports;

use App\Models\StockMovement;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class StockInExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return StockMovement::with('product', 'user')->where('type', 'in')->latest()->get();
    }

    public function headings(): array
    {
        return ['No', 'Produk', 'Jumlah', 'Alasan', 'Catatan', 'Oleh', 'Tanggal'];
    }

    public function map($movement): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $movement->product->name ?? '-',
            '+' . $movement->quantity,
            $movement->reason ?? '-',
            $movement->notes ?? '-',
            $movement->user->name ?? '-',
            $movement->created_at->format('d M Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '22C55E']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}