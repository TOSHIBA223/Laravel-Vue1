<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Builders\ContentTags;
use App\Models\SMSTemplate;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function index($id)
    {
        echo '<pre>';
        print_r(ContentTags::buildMessage(SMSTemplate::find(3)->content, 1, 1));
        echo '</pre>';


    }
}
