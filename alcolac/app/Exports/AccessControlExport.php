<?php

namespace App\Exports;

use App\Models\QuestionnaireSent;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AccessControlExport implements FromCollection
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

        return $this->data;
    }
}
