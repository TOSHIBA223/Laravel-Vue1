<?php

namespace App\Imports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use App\Models\Users;
use App\Models\Address;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SunshineUserImport implements ToCollection, WithHeadingRow
{
    /**
    * @param ToModel $model
    */

    private $employee_codes;
    public function collection(collection $rows)
    {
        foreach( $rows as $row ) {
            if (!empty($row['employee_id']) && !empty($row['mobile_no'])) {
                $dob = Carbon::createFromFormat('d/m/Y', $row['birth_date'])->format('Y-m-d');
               // $dob = date("Y-m-d", strtotime($row['birth_date']));
                $email = $row['email'] ?? '';
                $password = 1234;
                $menu_roles = 'user';
                $mobile = $this->formatNumber($row['mobile_no']);

                $address = Address::updateOrCreate([
                    'address' => $row['street_address'],
                    'suburb' => $row['suburb'],
                    'post_code' => $row['post_code'],
                    'state' => $row['state'],
                    'country' => 'AU',
                ]);
                $getuserid =  Users::updateOrCreate([
                    'employee_code' => 'S_' . $row['employee_id'],
                ],
                    [
                        'first_name' => $row['given_names'],
                        'last_name' => $row['surname'],
                        'email' => $email,
                        'phone' => $mobile,
                        'location' => 'Sunshine',
                        'groups' => $row['employer_status'],
                        'dob' => $dob,
                        'is_inactive' => 0,
                        //'password' => $password,
                       // 'menuroles' => $menu_roles,
                        'address' => $address->id
                    ]);

                    Address::where([['id', $address->id]])->update(['user_id' => $getuserid->id]);

                $this->employee_codes[] = 'S_' . $row['employee_id'];
            }
        }
    }

    public function getEmployeeCodes()
    {
        return $this->employee_codes;
    }

    private function formatNumber($number)
    {
        $remove_spaces = str_replace(' ', '', $number);
        $remove_leading_zero = ltrim($remove_spaces, '0');
        $final_number = '+61' . $remove_leading_zero;

        return $final_number;
    }
}
