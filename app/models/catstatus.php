<?php
namespace Models;

abstract class CatStatus {
    const Lost = 0;
    const Found = 1;
    const InShelter = 2;

    public static function isValid(int $catStatus) : bool {
        switch ($catStatus) {
            case 0:
            case 1:
            case 2:
                return true;
        }
        return false;
    }
}

?>