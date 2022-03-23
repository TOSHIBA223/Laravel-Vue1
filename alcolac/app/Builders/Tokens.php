<?php
namespace App\Builders;

use App\Models\LoginToken;
use App\Models\File;

class Tokens
{
    /**
     * @var string[] $allowedTables Array of table names that can be accessed by the verification method
     * @see Tokens::verification()
     */
    private static $allowedTables = [
        'declaration',
        'poll'
    ];


    /**
     * Attempts to generate a login token for a user, which includes 6 digits.
     * If this fails, we rerun the method to build a new token and test again.
     *
     * @return string The generated token
     */
    public static function login()
    {
        $characters = '0123456789';
        $token = self::buildToken($characters, 6);

        if( !LoginToken::where('token', $token)->exists() )
            return $token;

        self::login();
    }

    /**
     * @param string $type The type of verification token we're looking to build, for testing its current existance
     * @return string|string[] Returns the generated token, otherwise returns an array with an error message
     */
    public static function verification( $type = 'declaration')
    {
        if( in_array($type, self::$allowedTables) ) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $token = self::buildToken($characters, 8);
            $model = call_user_func_array('App\\Models\\' . ucfirst($type) . 'Sent::where', [
                [
                    'token' => $token,
                    'complete' => 0,
                    'void' => 0
                ]
            ]);
            $exists = $model->exists();

            if( !$exists )
                return $token;

            self::verification($type);
        } else
            return ['error' => "Table $type is not an accepted value"];
    }

    /**
     * Creates a token for the admin file system Viewers
     *
     * @return string The generated Token
     */
    public static function adminFiles()
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $token = self::buildToken($characters, 8);

        if( !File::where('token', $token)->exists() )
            return $token;

        self::adminFiles();
    }

    /**
     * @param String $characters The characters that are available to be used in building the token.
     *                              This should not contain any unsafe query_string characters that are escaped in the URL
     * @param Integer $tokenLength The expected output length of the token string
     * @return string A randomised set of characters
     */
    private static function buildToken(String $characters, int $tokenLength)
    {
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $tokenLength; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

}
