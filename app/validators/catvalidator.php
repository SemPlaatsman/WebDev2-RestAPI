<?php
namespace Validators;

use Models\Cat;
use Models\CatStatus;

class CatValidator {
    public static function isValid(Cat $cat) : bool {
        if (self::validateId($cat->id)) {
            return false;
        } else if (self::validateUserId($cat->userId)) {
            return false;
        } else if (self::validateImageFormat($cat->imageFormat)) {
            return false;
        } else if (self::validateBreed($cat->breeds)) {
            return false;
        } else if (self::validateDescription($cat->description)) {
            return false;
        } else if (self::validateStatus($cat->status)) {
            return false;
        }
        return true;
    }

    private static function validateId(int $id) : bool {
        return $id < 1 || $id > 9999999999;
    }

    private static function validateUserId(int $userId) : bool {
        return self::validateId($userId);
    }

    private static function validateImageFormat(string $imageFormat) : bool {
        return !in_array($imageFormat, ["png", "jpg", "jpeg", "gif"]);
    }

    private static function validateBreed(array $breeds) : bool {
        return strlen(implode(',', $breeds)) >= 255;
    }

    private static function validateDescription(string $description) : bool {
        return strlen($description) >= 512;
    }

    private static function validateStatus(int $status) : bool {
        return !CatStatus::isValid($status);
    }
}

?>