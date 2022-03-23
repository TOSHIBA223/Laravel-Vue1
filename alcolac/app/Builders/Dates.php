<?php
namespace App\Builders;

class Dates
{

    public static function days()
    {
        $days = range(1,31);

        foreach( $days as $index => $day )
        {
            $days[$index] = str_pad($day, 2, '0', STR_PAD_LEFT);
        }

        return $days;
    }

    public static function months()
    {
        return [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December',
        ];
    }

    public static function years()
    {

        $date = date('Y', strtotime('today -15 years'));
        $years = [];
        for($i = $date; $i >= 1920; $i--) {
            $years[] = $i;
        }

        return $years;
    }
}
