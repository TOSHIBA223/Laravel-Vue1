<?php
namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Builders\ContentTags;

class TagBuilder extends AdminController
{

    public function buildMessageFrontend(Request $r)
    {
        switch( $r->type )
        {
            case 'user':
                $message = self::buildUserMessage($r);
                break;
            case 'group':
                $message = self::buildGroupMessage($r);
                break;
            case 'location':
                $message = self::buildLocationMessage($r);
                break;
        }

        $message = str_replace("\n", '<br>', $message);

        return back()->with(['builtMessage' => $message]);
    }

    private function buildUserMessage(Request $r)
    {
        if( in_array('all', $r->users) ) {
            $user_id = User::first()->id;
            return ContentTags::buildMessage($r->message, $user_id);
        }

        return ContentTags::buildMessage($r->message, $r->users[0]);

    }

    private function buildGroupMessage(Request $r)
    {
        if( $r->group === 'all' ) {
            $user_id = User::first()->id;
            return ContentTags::buildMessage($r->message, $user_id);
        }

        $user_id = User::where('groups', $r->group)->first()->id;
        return ContentTags::buildMessage($r->message, $user_id);

    }

    private function buildLocationMessage(Request $r)
    {
        if( $r->location === 'all' ) {
            $user_id = User::first()->id;
            return ContentTags::buildMessage($r->message, $user_id);
        }

        $user_id = User::where('location', $r->location)->first()->id;
        return ContentTags::buildMessage($r->message, $user_id);

    }
}
