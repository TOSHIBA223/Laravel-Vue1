<?php

namespace App\Imports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use App\Models\Users;
use App\Models\Address;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ColacUserImport implements ToCollection, WithHeadingRow
{
    /**
    * @param ToModel $model
    */

    private $employee_codes;
    public function collection(collection $rows)
    {
        foreach( $rows as $row ) {
            $date = Carbon::now('Australia/Melbourne')->format('Y-m-d');
            $time = Carbon::now('Australia/Melbourne')->format('H:i:s');
            if( !empty($row['code']) && !empty($row['mobile_no'])) {

                file_put_contents(
                    storage_path() . "/logs/import_test_". $date . '.log',
                    $time . ' ' . 'code is ' . $row['code'] . "\n",
                    FILE_APPEND
                );
                $dob = Carbon::createFromFormat('d/m/Y', $row['birth_date'])->format('Y-m-d');
               // $dob = date("Y-m-d", strtotime($row['birth_date']));
                $email = $row['email'] ?? '';
                $password = 1234;
                $menu_roles = 'user';

                $admin_users = [
                    '2242',
                    '123',
                    '3807'
                ];

                $address = Address::updateOrCreate([
                    'address' => $row['street_address'],
                    'suburb' => $row['suburb'],
                    'post_code' => $row['post_code'],
                    'state' => 'Vic',
                    'country' => 'AU',
                ]);


                if (in_array($row['code'], $admin_users)) {
                   $getuserid =  Users::updateOrCreate([
                        'employee_code' => (string)$row['code'],
                    ],
                        [
                            'first_name' => $row['statutory_given_names'],
                            'last_name' => $row['statutory_family_name'],
                            'email' => $email,
                            'phone' => '+61' . $row['mobile_no'],
                            'location' => 'Colac',
                            'groups' => $row['paylocation'],
                            'dob' => $dob,
                            'is_inactive' => 0,
                            'address' => $address->id
                        ]);

                        Address::where([['id', $address->id]])->update(['user_id' => $getuserid->id]);

                } else {
                    $getuserid = Users::updateOrCreate([
                        'employee_code' => $row['code'],
                    ],
                        [
                            'first_name' => $row['statutory_given_names'],
                            'last_name' => $row['statutory_family_name'],
                            'email' => $email,
                            'phone' => '+61' . $row['mobile_no'],
                            'location' => 'Colac',
                            'groups' => $row['paylocation'],
                            'dob' => $dob,
                            'is_inactive' => 0,
                            //'password' => $password,
                           // 'menuroles' => $menu_roles,
                            'address' => $address->id
                        ]);
                        Address::where([['id', $address->id]])->update(['user_id' => $getuserid->id]);
                }

                $this->employee_codes[] = (string)$row['code'];
            }
        }
    }

    public function getEmployeeCodes()
    {
        return $this->employee_codes;
    }
}
