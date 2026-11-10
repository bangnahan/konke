<?php

namespace App\Services;

use App\Models\Event;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportService
{
    /**
     * Export attendees to CSV file.
     */
    public function exportCsv(Event $event): StreamedResponse
    {
        $filename = 'rekap-kehadiran-'.$event->slug.'-'.now()->format('Ymd_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($event) {
            $handle = fopen('php://output', 'w');

            // Add UTF-8 BOM for Microsoft Excel readability
            fwrite($handle, "\xEF\xBB\xBF");

            // Header row
            fputcsv($handle, [
                'No',
                'Kode QR/Token',
                'Nama Lengkap',
                'Email',
                'Nomor WhatsApp',
                'Instansi (Wajihah)',
                'Jabatan (Amanah)',
                'Status Konfirmasi',
                'Status Check-in',
                'Waktu Check-in',
                'Waktu Pengisian',
            ]);

            $no = 1;
            $event->attendances()->chunk(200, function ($attendances) use ($handle, &$no) {
                foreach ($attendances as $row) {
                    fputcsv($handle, [
                        $no++,
                        $this->sanitize($row->qr_code_token),
                        $this->sanitize($row->full_name),
                        $this->sanitize($row->email),
                        $this->sanitize($row->phone_number),
                        $this->sanitize($row->institution),
                        $this->sanitize($row->position),
                        $this->sanitize($row->status_label),
                        $row->is_checked_in ? 'Sudah Check-in' : 'Belum Check-in',
                        $row->checked_in_at ? $row->checked_in_at->format('d/m/Y H:i:s') : '-',
                        $row->attended_at ? $row->attended_at->format('d/m/Y H:i:s') : '-',
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export attendees to Excel (.xlsx).
     */
    public function exportXlsx(Event $event): StreamedResponse
    {
        $filename = 'rekap-kehadiran-'.$event->slug.'-'.now()->format('Ymd_His').'.xlsx';

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Daftar Kehadiran');

        // Document Title
        $sheet->setCellValue('A1', 'DAFTAR KEHADIRAN EVENT');
        $sheet->setCellValue('A2', 'Nama Acara: '.$event->title);
        $sheet->setCellValue('A3', 'Tanggal Acara: '.($event->event_date ? $event->event_date->format('d F Y') : '-'));
        $sheet->setCellValue('A4', 'Lokasi: '.($event->location ?? '-'));
        $sheet->setCellValue('A5', 'Waktu Unduh: '.now()->format('d/m/Y H:i:s'));

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2:A5')->getFont()->setSize(11);

        // Table Header
        $headers = [
            'A7' => 'No',
            'B7' => 'Token QR',
            'C7' => 'Nama Lengkap',
            'D7' => 'Email',
            'E7' => 'Nomor WhatsApp',
            'F7' => 'Instansi (Wajihah)',
            'G7' => 'Jabatan (Amanah)',
            'H7' => 'Status Konfirmasi',
            'I7' => 'Status Check-in',
            'J7' => 'Waktu Check-in',
            'K7' => 'Waktu Registrasi',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Header Styling
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E293B'], // Dark slate
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CBD5E1'],
                ],
            ],
        ];
        $sheet->getStyle('A7:K7')->applyFromArray($headerStyle);
        $sheet->getRowDimension(7)->setRowHeight(28);

        $rowNum = 8;
        $no = 1;
        $attendees = $event->attendances()->get();

        foreach ($attendees as $item) {
            $sheet->setCellValue('A'.$rowNum, $no++);
            $sheet->setCellValue('B'.$rowNum, $item->qr_code_token);
            $sheet->setCellValue('C'.$rowNum, $item->full_name);
            $sheet->setCellValue('D'.$rowNum, $item->email);
            $sheet->setCellValueExplicit('E'.$rowNum, $item->phone_number, DataType::TYPE_STRING);
            $sheet->setCellValue('F'.$rowNum, $item->institution);
            $sheet->setCellValue('G'.$rowNum, $item->position);
            $sheet->setCellValue('H'.$rowNum, $item->status_label);
            $sheet->setCellValue('I'.$rowNum, $item->is_checked_in ? 'Sudah Check-in' : 'Belum Check-in');
            $sheet->setCellValue('J'.$rowNum, $item->checked_in_at ? $item->checked_in_at->format('d/m/Y H:i:s') : '-');
            $sheet->setCellValue('K'.$rowNum, $item->attended_at ? $item->attended_at->format('d/m/Y H:i:s') : '-');

            // Row Zebra coloring
            if ($rowNum % 2 === 0) {
                $sheet->getStyle("A{$rowNum}:K{$rowNum}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F8FAFC');
            }

            // Check-in status badge highlight in excel
            if ($item->is_checked_in) {
                $sheet->getStyle("I{$rowNum}")->getFont()->getColor()->setRGB('059669'); // Green
            }

            $sheet->getStyle("A{$rowNum}:K{$rowNum}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('E2E8F0');
            $rowNum++;
        }

        // Auto-fit columns
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', "attachment; filename=\"{$filename}\"");
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }

    /**
     * Export attendees to PDF document.
     */
    public function exportPdf(Event $event)
    {
        $filename = 'laporan-kehadiran-'.$event->slug.'-'.now()->format('Ymd_His').'.pdf';
        $attendees = $event->attendances()->get();

        $pdf = Pdf::loadView('reports.attendance_pdf', [
            'event' => $event,
            'attendees' => $attendees,
            'totalRegistered' => $attendees->count(),
            'totalCheckedIn' => $attendees->where('is_checked_in', true)->count(),
            'totalHadir' => $attendees->where('attendance_status', 'hadir')->count(),
            'totalTerlambat' => $attendees->where('attendance_status', 'hadir_terlambat')->count(),
            'totalTidakHadir' => $attendees->where('attendance_status', 'tidak_hadir')->count(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download($filename);
    }

    /**
     * Sanitize string against CSV Formula Injection.
     */
    protected function sanitize(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        $value = (string) $value;
        if (str_starts_with($value, '=') || str_starts_with($value, '+') || str_starts_with($value, '-') || str_starts_with($value, '@')) {
            return "'".$value;
        }

        return $value;
    }
}
