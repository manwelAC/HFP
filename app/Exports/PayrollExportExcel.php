<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class PayrollExportExcel implements FromCollection, WithHeadings, WithEvents
{
    protected $data;
    protected $header;
    protected $company;
    protected $period;
    protected $payDate;
    protected $logo;

    public function __construct($data, $header, $company, $period, $payDate, $logo = null)
    {
        $this->data = $data;
        $this->header = $header;
        $this->company = $company;
        $this->period = $period;
        $this->payDate = $payDate;
        $this->logo = $logo;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return $this->header;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                $worksheet = $sheet->getDelegate();

                // Insert 4 rows before the actual header
                $sheet->insertNewRowBefore(1, 4);

                // Add logo if provided
                if ($this->logo && file_exists($this->logo)) {
                    $drawing = new Drawing();
                    $drawing->setName('Logo');
                    $drawing->setDescription('Payslip Logo');
                    $drawing->setPath($this->logo);
                    $drawing->setHeight(80);
                    $drawing->setCoordinates('A1');
                    $drawing->setWorksheet($worksheet);
                }

                // Set company, period, and pay date in column D
                $sheet->setCellValue('E1', $this->company);
                $sheet->setCellValue('E2', $this->period);
                $sheet->setCellValue('E3', $this->payDate);

                // Style the first 3 rows (bold)
                $sheet->getStyle('E1:E3')->getFont()->setBold(true);
            },
        ];
    }
}
