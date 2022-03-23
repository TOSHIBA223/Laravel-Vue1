<?php

namespace App\Exports;

use App\Models\Declaration;
use App\Models\DeclarationSent;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DeclarationExport implements FromCollection, WithHeadings
{

    // TODO Final testing required after items are added to DB
    public function __construct($id, $dateStart = false, $dateEnd = false)
    {
        $this->id = $id;
        $this->dateStart = $dateStart;
        $this->dateEnd = $dateEnd;

        $declaration_fields = json_decode(Declaration::find($id)->fields);

        foreach($declaration_fields as $field) {
            $this->fieldHeaders[] = $field->name;
        }
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $declarations =  (new DeclarationSent)->getForCSV($this->id, $this->dateStart, $this->dateEnd);
        foreach($declarations as $dec)
        {
            $answerArray = json_decode($dec->answers, true);
            $dec->created_at = Carbon::parse($dec->created_at)->format('d/m/Y H:i');
            $dec->updated_at = $dec->complete === 1 ? Carbon::parse($dec->updated_at)->format('d/m/Y H:i') : 'N/A';
            $dec->complete = $dec->complete === 1 ? 'Yes' : 'No';
            $dec->void = $dec->void === 1 ? 'Yes' : 'No';
            $dec->passed = $dec->passed === 1 ? 'Yes' : 'No';

            if(is_array($answerArray)) {
                foreach ($answerArray as $key => $answer) {
                    $dec->$key = $answer;
                }
            }
            unset($dec->answers);
        }

        return $declarations;
    }

    public function headings(): array
    {
        $headingsArray = [
            'Declaration Date', 'Completed Date', 'Declaration ID', 'Employee Code', 'First Name', 'Surname',
            'Phone', 'Complete', 'Void', 'Groups', 'Location', 'Passed'
        ];
        foreach( $this->fieldHeaders as $field) {
            array_push($headingsArray, $field);
        }
        return $headingsArray;
    }
}
