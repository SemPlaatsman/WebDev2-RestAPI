<?php
namespace Models;

abstract class Roles {
    const Customer = 0;
    const Employee = 1;

    public static function isValid(int $role) : bool {
        switch ($role) {
            case 0:
            case 1:
                return true;
        }
        return false;
    }
}

?>