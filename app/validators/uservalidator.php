<?php
namespace Validators;

use Models\User;
use Models\Roles;

class UserValidator {
    public static function isValid(User $user) : bool {
        if (self::validateId($user->id)) {
            return false;
        } else if (self::validateUsername($user->username)) {
            return false;
        } else if (self::validatePassword($user->password)) {
            return false;
        } else if (self::validateRole($user->role)) {
            return false;
        } else if (self::validateEmail($user->role)) {
            return false;
        }
        return true;
    }

    private static function validateId(int $id) : bool {
        return $id < 1 || $id > 9999999999;
    }

    private static function validateUsername(string $username) : bool {
        return strlen($username) < 3 || strlen($username) > 255;
    }
    
    private static function validatePassword(string $password) : bool {
        return strlen($password) < 6 || strlen($password) > 255;
    }

    private static function validateRole(int $role) : bool {
        return !Roles::isValid($role);
    }

    private static function validateEmail(string $email) : bool {
        return !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) >= 255;
    }
}

?>