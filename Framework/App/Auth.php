<?php

namespace Framework\App;

use Framework\Helpers\Helper;
use Framework\Http\Request;
use Framework\Traits\Singleton;
use Framework\Database\DB;
use Framework\Database\Table;

class Auth {

    use Singleton;

    protected static $sessionName;

    protected static $user;

    protected function __construct($sessionName) {

        self::$sessionName = $sessionName;
        
        ini_set('session.name', self::$sessionName);
        session_start();
    }

    public static function login($email, $password) {
        // находим юзера по парлю и емейлу
        // генерим хэш записываем в базу и в сессию
        // если нужно получить текущего юзера, чекаем хэш и по нему достаем из базы


        $user = DB::table(Table::UsersTable())->where([
            'email' => $email,
            'password' => $password
        ])->find_one();

        if($user !== false) {
            self::$user = $user;

            $hash = md5($user->email . $user->id);
            $_SESSION['auth'] = $hash;
            $user->hash = $hash;
            $user->save();

            return self::$user;
        }

        return null;
    }

    public static function user() {

        if(self::$user)
            return self::$user;


        if(isset($_SESSION['auth'])){

            $hash = $_SESSION['auth'];

            $user = DB::table(Table::UsersTable())->where([
                'hash' => $hash
            ])->find_one();

            if($user !== false) {
                self::$user = $user;
                return self::$user;
            }

        }
        return null;
    }

    public static function logout() {
        unset($_SESSION['auth']);
    }





}