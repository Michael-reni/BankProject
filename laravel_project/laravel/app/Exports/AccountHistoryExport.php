<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithPreCalculateFormulas;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class AccountHistoryExport implements 
FromView,
ShouldAutoSize,
WithCalculatedFormulas,
WithPreCalculateFormulas,
WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View 
    {
       
        
        return view('banking_system.account_history_xlsx',['data' => $this->data,'count' => $this->count ]);
    }
    public function __construct($data,$count)
    {
        $this->data = $data;
        $this->count = $count;
    }

    public function registerEvents() :array
    {

        return [ 
            AfterSheet::class => function(AfterSheet $event){

                $cell = $event->sheet->getDelegate()->getHighestRowAndColumn();
              // dd('B'.($cell['row']-2).':'.$cell['column'].($cell['row']-2));

              //dd('D'.$cell['row']);
                $event->sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'italic' => true
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT
                    ]


                ]);

                $event->sheet->getStyle('A2:C2')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'italic' => true
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT
                    ]


                ]);

            }

        ];
    }

}
