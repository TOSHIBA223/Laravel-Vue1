<?php

namespace App\Exports;

use App\Models\DeclarationSent;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AccessControlExportDec implements FromCollection
{

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        foreach($this->data as $dec)
        {
            $dec->user_id = $dec->user_id;
            $dec->complete = $dec->complete === 1 ? 'True' : 'False';


        }
        return $this->data;
    }
}
