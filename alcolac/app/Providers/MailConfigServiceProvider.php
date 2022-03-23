<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Settings;
use Config;

class MailConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $host = Settings::where('name', 'SMTP HOST')->first()->value;
        if (!$host) {
            return;
        }
        $port = Settings::where('name', 'SMTP Port')->first()->value;
        $username = Settings::where('name', 'SMTP Username')->first()->value;
        $password = Settings::where('name', 'SMTP Password')->first()->value;
        $from_address = Settings::where('name', 'From Email')->first()->value;
        $from_name = Settings::where('name', 'From Name')->first()->value;
        $config = array(
            'driver'     => 'smtp',
            'host'       => $host,
            'port'       => $port,
            'username'   => $username,
            'password'   => $password,
            'from'       => array('address' => $from_address, 'name' => $from_name),
        );

        Config::set('mail', $config);
    }
}
