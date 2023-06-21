<?php
namespace Validators;

use DateTime;
use Models\Appointment;

class AppointmentValidator {
    public static function isValid(Appointment $appointment) : bool {
        if (self::validateId($appointment->id)) {
            return false;
        } else if (self::validateUserId($appointment->userId)) {
            return false;
        } else if (self::validateDateTime($appointment->datetime)) {
            return false;
        }
        return true;
    }

    private static function validateId(string $id) : bool {
        return strlen($id) < 1 || strlen($id) >= 16;
    }

    private static function validateUserId(int $userId) : bool {
        return $userId < 1 || $userId > 9999999999;
    }

    private static function validateDateTime(string $datetime) : bool {
        return !DateTime::createFromFormat('Y-m-d H:i:s', $datetime);
    }
}

?>