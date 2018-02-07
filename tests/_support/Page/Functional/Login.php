<?php
namespace Page\Functional;
use App\Models\Role;
use Socialite;
use Moc;
class Login
{
    // include url of current page
    public static $URL = '/';
    public static $LoginUrl = "/login";
    public static $SocialProvider = 'google';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     * public static $usernameField = '#username';
     * public static $formSubmitButton = "#mainForm input[type=submit]";
     */

    /**
     * Basic route example for your current URL
     * You can append any additional parameter to URL
     * and use it in tests like: Page\Edit::route('/123-post');
     */
    public static function route($param)
    {
        return static::$URL.$param;
    }

    /**
     * @var \FunctionalTester;
     */
    protected $functionalTester;

    public function __construct(\FunctionalTester $I)
    {
        $this->functionalTester = $I;
    }

    public static function getUserGoogle($id_user)
    {
        $userAttributes = [
            'id_user'           => $id_user,
            'username'          => 'juan.perez',
            'firstname'         => 'Juan',
            'lastname'          => 'Perez',
            'rut'               => '1-9',
            'email'             => 'ignacio.perez@schibsted.cl',
            'social_provider'   => self::$SocialProvider,
            'social_provider_id'=> '123456789',
            'url_avatar'        => 'google.com/avatar.jpg',
            'password'          => null,
            'id_area'           => 1
        ];

        return $userAttributes;
    }

    public static function getNoRegisterUserGoogle($id_user)
    {
        $userAttributes = [
            'id_user'           => $id_user,
            'username'          => 'juan.perez',
            'firstname'         => 'Juan',
            'lastname'          => 'Perez',
            'rut'               => '1-9',
            'email'             => 'abc@schibsted.cl',
            'social_provider'   => self::$SocialProvider,
            'social_provider_id'=> '123456789',
            'url_avatar'        => 'google.com/avatar.jpg',
            'password'          => null,
            'id_area'           => 1
        ];

        return $userAttributes;
    }




}
