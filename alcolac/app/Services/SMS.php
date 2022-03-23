<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use MessageMediaMessagesLib\Models;
use MessageMediaMessagesLib\MessageMediaMessagesClient;
use App\Builders\ContentTags;
use MessageMediaMessagesLib\Exceptions;
use App\Models\DeclarationSent;
use App\Models\Settings;
use App\Models\Declaration;


class SMS
{
    public $sourceNumber;
    protected $client;

    public function __construct($useHmacAuthentication = false)
    {
        $pubKey = env('SMS_PUB_KEY');
        $secretKey = env('SMS_SECRET_KEY');
        if (Settings::where('name', 'SMS PUBLIC API Key')->first()) {
            $pubKey = Settings::where('name', 'SMS PUBLIC API Key')->first()->value;
            $secretKey = Settings::where('name', 'SMS SECRET API Key')->first()->value;
        }
        $this->client = new MessageMediaMessagesClient($pubKey, $secretKey, $useHmacAuthentication);
        $this->sourceNumber = env('SMS_SOURCE_NUMBER');
        if (Settings::where('name', 'Sender ID')->first()) {
            $this->sourceNumber = Settings::where('name', 'Sender ID')->first()->value;
        }
    }

    //TODO: Make sure sending functionality works correctly on DEV

    public function sendSingleDec($message1, $userId, $phone, $token, $declarationid = 0, $templateId = 0)
    {
        //$message = ContentTags::buildMessage($message, $userId, $templateId);
        $messagesController = $this->client->getMessages();
        $body = new Models\SendMessagesRequest;

        $datasa = Declaration::where([
            ['id', $declarationid],
        ])->orderBy('created_at', 'desc')->first();

        if ($datasa->sms_enable == 'Enable') {
            $expire_hour_string = "";
            if ($datasa->expire_hour < 60) {
                $expire_hour_string = $datasa->expire_hour . " Minute";
            } else if ($datasa->expire_hour < 1440) {
                $expire_hour_string = ($datasa->expire_hour / 60) . " Hour";
            } else {
                $expire_hour_string = ($datasa->expire_hour / 1440) . " Day";
            }
            $message1 = str_replace("@Minute@", $expire_hour_string, $message1);
        } else {
            $message1 = str_replace("@Minute@", '15 Minutes', $message1);
        }

        $message = ContentTags::buildMessageDec($message1, $userId, $templateId, $declarationid);

        $body->messages = array();
        $body->messages[0] = new Models\Message();
        $body->messages[0]->content = $message;
        $body->messages[0]->destinationNumber = $phone;
        $body->messages[0]->sourceNumber = $this->sourceNumber;
        $body->messages[0]->sourceNumberType = 'ALPHANUMERIC';


        $messagesController->sendMessages($body);
    }

    public function sendSingle($message, $userId, $phone, $templateId = 0)
    {

        $message = ContentTags::buildMessage($message, $userId, $templateId);
        $messagesController = $this->client->getMessages();
        $body = new Models\SendMessagesRequest;

        $body->messages = array();
        $body->messages[0] = new Models\Message();
        $body->messages[0]->content = $message;
        $body->messages[0]->destinationNumber = $phone;
        //Live name
        $body->messages[0]->sourceNumber = $this->sourceNumber;
        $body->messages[0]->sourceNumberType = 'ALPHANUMERIC';


        try {
            $messagesController->sendMessages($body);
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function sendSingleUser($message, $userId, $phone, $templateId = 0)
    {

        $message = ContentTags::buildMessageAdmin($message, $userId, $templateId);
        $messagesController = $this->client->getMessages();
        $body = new Models\SendMessagesRequest;

        $body->messages = array();
        $body->messages[0] = new Models\Message();
        $body->messages[0]->content = $message;
        $body->messages[0]->destinationNumber = $phone;
        //Live name
        $body->messages[0]->sourceNumber = $this->sourceNumber;
        $body->messages[0]->sourceNumberType = 'ALPHANUMERIC';


        try {
            $messagesController->sendMessages($body);
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function sendSingleDecAdmin($message, $userId, $phone, $templateId = 0)
    {
        $datasa = Declaration::where([

            ['id', $templateId],

        ])->orderBy('created_at', 'desc')->first();

        if ($datasa->sms_enable == 'Enable') {
            $expire_hour_string = "";
            if ($datasa->expire_hour < 60) {
                $expire_hour_string = $datasa->expire_hour . " Minute";
            } else if ($datasa->expire_hour < 1440) {
                $expire_hour_string = ($datasa->expire_hour / 60) . " Hour";
            } else {
                $expire_hour_string = ($datasa->expire_hour / 1440) . " Day";
            }
            $message = str_replace("@Minute@", $expire_hour_string, $message);
        } else {
            $message = str_replace("@Minute@", '15 Minutes', $message);
        }

        $message = ContentTags::buildMessageAdmin($message, $userId, $templateId);
        $messagesController = $this->client->getMessages();
        $body = new Models\SendMessagesRequest;

        $body->messages = array();
        $body->messages[0] = new Models\Message();
        $body->messages[0]->content = $message;
        $body->messages[0]->destinationNumber = $phone;
        //Live name
        $body->messages[0]->sourceNumber = $this->sourceNumber;
        $body->messages[0]->sourceNumberType = 'ALPHANUMERIC';



        $messagesController->sendMessages($body);
    }

    public function sendMultiple($messageTemplate, $userList, $templateId = 0)
    {
        if (empty($userList)) return 'No Users Selected';

        $messagesController = $this->client->getMessages();
        //dd($userList);
        // Break up users into manageable API calls max 100 per message call
        $user_chunks = array_chunk($userList, 100);
        //$user_chunks = $userList;

        // dd($user_chunks);

        // build and send based on user chunks
        foreach ($user_chunks as $chunk) {
            $body = new Models\SendMessagesRequest;
            $body->messages = $this->createMessageChunk($messageTemplate, $chunk, $templateId);

            // $messagesController->sendMessages($body);

            try {
                $result = $messagesController->sendMessages($body);
            } catch (Exceptions\SendMessages400Response $e) {
                echo 'Caught SendMessages400Response: ',  $e->getMessage(), "\n";
            } catch (MessageMediaMessagesLib\APIException $e) {
                echo 'Caught APIException: ',  $e->getMessage(), "\n";
            }
        }
    }

    public function createMessageChunk($messageTemplate, $userListChunk, $templateId)
    {
        $message_body = [];

        foreach ($userListChunk as $index => $user) {
            $message = ContentTags::buildMessage($messageTemplate, $user->id, $templateId);

            $message_body[$index] = new Models\Message();
            $message_body[$index]->content = $message;
            $message_body[$index]->destinationNumber = $user->phone;
            //Live name
            $message_body[$index]->sourceNumber = $this->sourceNumber;
            $message_body[$index]->sourceNumberType = 'ALPHANUMERIC';
        }


        return $message_body;
    }



    public function sendMultipleSMS($messageTemplate, $userList, $templateId = 0)
    {
        if (empty($userList)) return 'No Users Selected';

        $messagesController = $this->client->getMessages();
        //dd($userList);
        // Break up users into manageable API calls max 100 per message call
        // $user_chunks = array_chunk($userList, 100);
        //$user_chunks = $userList;



        // build and send based on user chunks
        foreach ($userList as $chunk) {

            $body = new Models\SendMessagesRequest;
            $body->messages = $this->createMessageChunkSMS($messageTemplate, $userList, $chunk->id, $chunk->phone, $templateId);

            // $messagesController->sendMessages($body);

            try {
                $result = $messagesController->sendMessages($body);
            } catch (Exceptions\SendMessages400Response $e) {
                echo 'Caught SendMessages400Response: ',  $e->getMessage(), "\n";
                die;
            } catch (MessageMediaMessagesLib\APIException $e) {
                echo 'Caught APIException: ',  $e->getMessage(), "\n";
                die;
            }
        }
    }

    public function createMessageChunkSMS($messageTemplate, $userList, $userid, $phone, $templateId)
    {
        $message_body = [];

        foreach ($userList as $index => $user) {
            $message = ContentTags::buildMessage($messageTemplate, $userid,  $templateId);

            $message_body[$index] = new Models\Message();
            $message_body[$index]->content = $message;
            $message_body[$index]->destinationNumber = $phone;
            //Live name
            $message_body[$index]->sourceNumber = $this->sourceNumber;
            $message_body[$index]->sourceNumberType = 'ALPHANUMERIC';
        }

        return $message_body;
    }



    public function sendMultipletest($messageTemplate, $userList, $templateId = 0)
    {


        if (empty($userList)) return 'No Users Selected';

        $messagesController = $this->client->getMessages();


        // Break up users into manageable API calls max 100 per message call
        $user_chunks = array_chunk($userList, 100);
        //$user_chunks = $userList;



        // build and send based on user chunks
        foreach ($user_chunks as $chunk) {
            $body = new Models\SendMessagesRequest;
            $body->messages = $this->createMessageChunk1($messageTemplate, $chunk, $templateId);
            // $messagesController->sendMessages($body);

            try {
                $result = $messagesController->sendMessages($body);
            } catch (Exceptions\SendMessages400Response $e) {

                echo 'Caught SendMessages400Response: ',  $e->getMessage(), "\n";
            } catch (MessageMediaMessagesLib\APIException $e) {

                echo 'Caught APIException: ',  $e->getMessage(), "\n";
            }
        }
    }

    public function createMessageChunk1($messageTemplate, $userListChunk, $templateId)
    {
        $message_body = [];


        foreach ($userListChunk as $index => $user) {

            $dec = DeclarationSent::where('user_id', $user->id)->orderBy('created_at', 'DESC')->first();

            if ($templateId == 0) {
                $templateId = $dec->declaration_id;
            }

            $message1 = ContentTags::buildMessageAdmin($messageTemplate, $user->id, $templateId);
            //$message = str_replace("##name##", $user->name, $message1);
            // $message = str_replace("##token##", $dec->token, $message);
            //$message = str_replace("##token##", "sdfds", $message);

            $message_body[$index] = new Models\Message();
            $message_body[$index]->content = $message1;
            $message_body[$index]->destinationNumber = $user->phone;
            //Live name
            $message_body[$index]->sourceNumber = $this->sourceNumber;
            $message_body[$index]->sourceNumberType = 'ALPHANUMERIC';
        }


        return $message_body;
    }

    public function sendMultipletestCron($messageTemplate, $userList, $declarationId)
    {
        if (empty($userList)) return 'No Users Selected';

        $messagesController = $this->client->getMessages();

        // Break up users into manageable API calls max 100 per message call
        $user_chunks = array_chunk($userList, 100);
        //$user_chunks = $userList;



        // build and send based on user chunks
        foreach ($user_chunks as $chunk) {
            $body = new Models\SendMessagesRequest;
            $body->messages = $this->createMessageChunkCron($messageTemplate, $chunk, $declarationId);
            // $messagesController->sendMessages($body);

            try {
                $result = $messagesController->sendMessages($body);
                error_log("Successfully Sent SMS:" . json_encode($chunk));
            } catch (Exceptions\SendMessages400Response $e) {
                echo 'Caught SendMessages400Response: ',  $e->getMessage(), "\n";
            } catch (MessageMediaMessagesLib\APIException $e) {
                echo 'Caught APIException: ',  $e->getMessage(), "\n";
            }
        }
    }

    public function createMessageChunkCron($messageTemplate, $userListChunk, $declarationId)
    {
        $message_body = [];


        foreach ($userListChunk as $index => $user) {

            $dec = DeclarationSent::where('declaration_id', $declarationId)->orderBy('created_at', 'DESC')->first();


            $message1 = ContentTags::buildMessageDec($messageTemplate, $user->id, 0, $declarationId);
            $message = str_replace("##name##", $user->first_name, $message1);
            $message = str_replace("##token##", $dec->token, $message);

            $message_body[$index] = new Models\Message();
            $message_body[$index]->content = $message;
            $message_body[$index]->destinationNumber = $user->phone;
            //Live name
            $message_body[$index]->sourceNumber = $this->sourceNumber;
            $message_body[$index]->sourceNumberType = 'ALPHANUMERIC';
        }

        error_log('message surendra');
        error_log(json_encode($message_body));

        return $message_body;
    }
}
