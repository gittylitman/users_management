<?php

namespace App\Filament\Services;

use App\Models\Request;
use App\Models\User;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;

class ExportRequests
{
    use Exportable;

    public function __invoke(HttpRequest $request)
    {   $data = [[]];
        $rows = $request->input('custom_data');
        if($rows !== null)    {
            if(sizeof($rows[0]) === 6){
                $data = User::all(); 
            }else{
                $data = Request::all(); 
            }
        }    
        return Excel::download(new class($data, $rows) implements FromCollection {
            private $data, $rows;
        
            public function __construct($data, $rows)
            {
                $this->data = $data;
                $this->rows = $rows;

            }
        
            public function collection()
            {
                foreach ($this->data as $item) {
                    $this->rows[] = $item;
                }
        
                return collect($this->rows);
            }
        }, 'requests.xlsx');
    }
}
