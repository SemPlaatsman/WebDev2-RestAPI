<?php

namespace Repositories;

use Exception;
use Models\Appointment;
use PDO;
use PDOException;
use Repositories\Repository;

class AppointmentRepository extends Repository {
    function getAll(int $offset = NULL, int $limit = NULL) : array {
        $query = "SELECT id, user_id, datetime FROM appointments" . " " . (isset($limit) ? "LIMIT :limit" : "LIMIT 12") . " " . (isset($offset) ? "OFFSET :offset" : "OFFSET 0");
        $stmt = $this->connection->prepare($query);
        isset($limit) ? $stmt->bindParam(':limit', $limit, PDO::PARAM_INT) : NULL;
        isset($offset) ? $stmt->bindParam(':offset', $offset, PDO::PARAM_INT) : NULL;
        $stmt->execute();

        $appointments = array();
        while (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {               
            $appointments[] = $this->rowToAppointment($row);
        }

        return $appointments;
    }

    function getOne(string $id) : Appointment {
        $stmt = $this->connection->prepare("SELECT `id`, `user_id`, `datetime` FROM `appointments` WHERE id=:id LIMIT 1");
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);
        $stmt->execute();

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $row = $stmt->fetch();
        $appointment = $this->rowToAppointment($row);

        return $appointment;
    }

    function insert(Appointment $appointment) : Appointment {
        $stmt = $this->connection->prepare("INSERT INTO `appointments`(`id`, `user_id`, `datetime`) VALUES (:id, :user_id, :datetime)");
        $stmt->bindParam(":id", $appointment->id, PDO::PARAM_STR);
        $stmt->bindParam(":user_id", $appointment->userId, PDO::PARAM_STR);
        $stmt->bindParam(":datetime", $appointment->datetime, PDO::PARAM_STR);
        $stmt->execute();
        return $this->getOne($appointment->id);
    }


    function update(Appointment $appointment, string $id) : Appointment {
        $stmt = $this->connection->prepare("UPDATE `appointments` SET `datetime`=:datetime WHERE id=:id");
        $stmt->bindParam(":datetime", $appointment->datetime, PDO::PARAM_STR);
        $stmt->bindParam(":id", $id, PDO::PARAM_STR);
        $stmt->execute();
        return $this->getOne($appointment->id);
    }

    function delete(string $id) : bool {
        $stmt = $this->connection->prepare("DELETE FROM `appointments` WHERE id = :id LIMIT 1");
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    private function rowToAppointment($row) : Appointment {
        return new Appointment($row['id'] ?? NULL, $row['user_id'] ?? NULL, $row['datetime'] ?? NULL);
    }
}
