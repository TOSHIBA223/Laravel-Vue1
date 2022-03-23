<?php

namespace App\Exports;

use App\Models\QuestionnaireSent;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SunshineGeneralCSV implements FromCollection, WithHeadings
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
            $answerArray = json_decode($dec->answers, true);
            $contact = data_get($answerArray, 'contact');
            $symptoms = data_get($answerArray, 'symptoms');
            $contact_live = data_get($answerArray, 'contact_live');
            $dec->created_at = Carbon::parse($dec->created_at)->format('d/m/Y H:i');
            $dec->updated_at = $dec->complete === 1 ? Carbon::parse($dec->updated_at)->format('d/m/Y H:i') : 'N/A';
            $dec->complete = $dec->complete === 1 ? 'Yes' : 'No';
            $dec->contact = $contact;
            $dec->symptoms = $symptoms;
            $dec->contact_live = $contact_live;
            $dec->passed = 'Yes';

            if( $contact == 'yes' ||
                $symptoms == 'yes' ||
                $contact_live == 'yes' || $dec->complete === 'No')
            {
                $dec->passed = 'No';
            }
            unset($dec->answers);
        }
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Declaration Date', 'Completed Date', 'Declaration ID', 'Employee Code', 'First Name', 'Surname',
            'Phone', 'Complete', 'Groups', 'Location', 'Contact', 'Symptoms',
            'Live With Contact', 'Passed'
        ];
    }
}
