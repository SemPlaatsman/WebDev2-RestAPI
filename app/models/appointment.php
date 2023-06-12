<?php
namespace Models;

use DateTime;

class Appointment {

    public string $id;
    public int $userId;
    public string $datetime;

    function __construct(string $id = NULL, int $userId = NULL, string $datetime = NULL) {
        if (isset($id)) $this->id = $id;
        if (isset($userId)) $this->userId = $userId;
        if (isset($datetime)) $this->datetime = $datetime;
    }

    public function getDateTime() {
        return DateTime::createFromFormat('Y-m-d H:i:s', $this->datetime);
    }
}

?>