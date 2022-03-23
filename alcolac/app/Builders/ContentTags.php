<?php

namespace App\Builders;

use Carbon\Carbon;
use App\Models;

class ContentTags
{

    //TODO add allowed values for each of the message builders here and check to see if they exist before pulling data
    private $allowedValues = [];

    static public function getContentTags()
    {
        $content_tags = include base_path() . '/config/contentTags.php';
        return $content_tags;
    }

    /**
     * @param $message String The original message template with Tags
     * @param $userId Integer The users ID we are grabbing information for
     * @param $templateId Integer The template id for the message being built. Refers to declaration or poll ID
     * @param array $options Not currently used
     * @return string returns the formatted message with all tags built
     */
    static public function buildMessage($message, $userId, $templateId = 0, array $options = [])
    {
        $matched_variables = [];
        preg_match_all('/@(\S+)@/', $message, $matched_variables);
        unset($matched_variables[0]);

        $days = 1;

        if (isset($options['date']['daysToAdd']))
            $days = $options['date']['daysToAdd'];

        $matched_variables = array_values(array_unique($matched_variables[1]));

        foreach ($matched_variables as $variable) {
            $breakdown = explode('|', $variable);
            switch ($breakdown[0]) {
                case 'Users':
                    $variable_replacement = Self::userBuilder($userId, $breakdown[1]);
                    break;
                case 'Dates':
                    $variable_replacement = Self::dateBuilder($breakdown[1], $days);
                    break;
                case 'Declarations':
                    $variable_replacement = Self::declarationBuilder($userId, $templateId);
                    break;
                case 'Reset Address':
                    $variable_replacement = Self::resetAddressBuilder($userId);
                    break;
                case 'Polls':
                    $variable_replacement = Self::pollBuilder($userId, $templateId);
                    break;
                case 'LoginTokens':
                    $variable_replacement = Self::loginTokenBuilder($userId);
                    break;
            }

            $message = str_replace("@$variable@", $variable_replacement, $message);
        }

        return $message;
    }


    static public function buildMessageAdmin($message, $userId, $templateId = 0, array $options = [])
    {
        $matched_variables = [];
        preg_match_all('/@(\S+)@/', $message, $matched_variables);
        unset($matched_variables[0]);

        $days = 1;

        if (isset($options['date']['daysToAdd']))
            $days = $options['date']['daysToAdd'];

        $matched_variables = array_values(array_unique($matched_variables[1]));

        foreach ($matched_variables as $variable) {
            $breakdown = explode('|', $variable);
            switch ($breakdown[0]) {
                case 'Users':
                    $variable_replacement = Self::userBuilderDec($userId, $breakdown[1]);
                    break;
                case 'Dates':
                    $variable_replacement = Self::dateBuilder($breakdown[1], $days);
                    break;
                case 'Declarations':
                    $variable_replacement = Self::declarationBuilder($userId, $templateId);
                    break;
                case 'Polls':
                    $variable_replacement = Self::pollBuilder($userId, $templateId);
                    break;
                case 'LoginTokens':
                    $variable_replacement = Self::loginTokenBuilder($userId);
                    break;
            }

            $message = str_replace("@$variable@", $variable_replacement, $message);
        }

        return $message;
    }


    static public function buildMessageDec($message, $userId, $templateId = 0, $declarationid, array $options = [])
    {


        $matched_variables = [];
        preg_match_all('/@(\S+)@/', $message, $matched_variables);
        unset($matched_variables[0]);

        $days = 1;

        if (isset($options['date']['daysToAdd']))
            $days = $options['date']['daysToAdd'];

        $matched_variables = array_values(array_unique($matched_variables[1]));

        foreach ($matched_variables as $variable) {
            $breakdown = explode('|', $variable);
            switch ($breakdown[0]) {
                case 'Users':
                    $variable_replacement = Self::userBuilderDec($userId, $breakdown[1]);
                    break;
                case 'Dates':
                    $variable_replacement = Self::dateBuilder($breakdown[1], $days);
                    break;
                case 'Declarations':
                    $variable_replacement = Self::declarationBuilderDec($userId, $templateId, $declarationid);
                    break;
                case 'Polls':
                    $variable_replacement = Self::pollBuilder($userId, $templateId);
                    break;
                case 'LoginTokens':
                    $variable_replacement = Self::loginTokenBuilder($userId);
                    break;
            }

            $message = str_replace("@$variable@", $variable_replacement, $message);
        }

        return $message;
    }


    static public function buildMessageNew($message, $userId, $templateId = 0, array $options = [])
    {
        $matched_variables = [];
        preg_match_all('/@(\S+)@/', $message, $matched_variables);
        unset($matched_variables[0]);

        $days = 1;

        if (isset($options['date']['daysToAdd']))
            $days = $options['date']['daysToAdd'];

        $matched_variables = array_values(array_unique($matched_variables[1]));

        foreach ($matched_variables as $variable) {
            $breakdown = explode('|', $variable);
            switch ($breakdown[0]) {
                case 'Users':
                    $variable_replacement = Self::userBuilder($userId, $breakdown[1]);
                    break;
                case 'Dates':
                    $variable_replacement = Self::dateBuilder($breakdown[1], $days);
                    break;
                case 'Declarations':
                    $variable_replacement = Self::declarationBuilder($userId, $templateId);
                    break;
                case 'Polls':
                    $variable_replacement = Self::pollBuilder($userId, $templateId);
                    break;
                case 'LoginTokens':
                    $variable_replacement = Self::loginTokenBuilder($userId);
                    break;
            }

            $message = str_replace("@$variable@", $variable_replacement, $message);
        }

        return $message;
    }

    static private function dateBuilder($tag, $days = 1)
    {
        // TODO add final date content tags
        switch ($tag) {
            case 'day':
                return Carbon::now('Australia/Melbourne')->addDays($days)->format('l');
                break;
            case 'date':
                return Carbon::now('Australia/Melbourne')->addDays($days)->format('jS');
                break;
            case 'monthFull':
                return Carbon::now('Australia/Melbourne')->addDays($days)->format('F');
                break;
        }
    }

    static private function userBuilder($userId, $value)
    {
        return Models\AdminUsers::where('id', $userId)->first()->$value;
    }

    static private function userBuilderDec($userId, $value)
    {
        return Models\User::where('id', $userId)->first()->$value;
    }

    static private function loginTokenBuilder($userId)
    {
        return Models\LoginToken::where('user_id', $userId)->first()->token;
    }

    static private function resetAddressBuilder($userId) 
    {
        $url = env('APP_URL');
        return $url . '/reset_address/' . Models\User::where('id', $userId)->first()->id;
    }

    static private function declarationBuilder($userId, $templateId)
    {
        $url = env('APP_URL');
        $token = Models\DeclarationSent::where([
            ['user_id', $userId],
            ['declaration_id', $templateId],

        ])->orderBy('created_at', 'desc')->first()->token;

        return $url . '/dec/' . $token;
    }

    static private function declarationBuilderDec($userId, $templateId, $declarationid)
    {
        $url = env('APP_URL');
        $token = Models\DeclarationSent::where([
            ['user_id', $userId],
            ['declaration_id', $declarationid],
            ['void', 0]
        ])->orderBy('created_at', 'desc')->first()->token;

        return $url . '/dec/' . $token;
    }

    static private function pollBuilder($userId, $templateId)
    {
        $item = Models\PollSent::where([
            ['user_id', $userId],
            ['poll_id', $templateId],
            ['void', 0]
        ])->orderBy('created_at', 'desc')->first();
        var_dump($item);
        die();
        $token = Models\PollSent::where([
            ['user_id', $userId],
            ['poll_id', $templateId],
            ['void', 0]
        ])->orderBy('created_at', 'desc')->first()->token;
        return '/poll/' . $token;
    }
}
