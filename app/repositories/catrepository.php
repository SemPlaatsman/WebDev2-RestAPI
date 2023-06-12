<?php

namespace Repositories;

use Exception;
use Models\Cat;
use PDO;
use PDOException;
use Repositories\Repository;

class CatRepository extends Repository {
    function getAll(int $status = NULL, int $offset = NULL, int $limit = NULL) : array {
        $query = "SELECT `id`, `breeds`, `description`, `status` FROM `cats`" . (isset($status) ? " WHERE `status`=:status" : "") . 
        (isset($limit) ? " LIMIT :limit" : " LIMIT 12") . (isset($offset) ? " OFFSET :offset" : " OFFSET 0");
        $stmt = $this->connection->prepare($query);
        isset($status) ? $stmt->bindParam(':status', $status, PDO::PARAM_INT) : NULL;
        isset($limit) ? $stmt->bindParam(':limit', $limit, PDO::PARAM_INT) : NULL;
        isset($offset) ? $stmt->bindParam(':offset', $offset, PDO::PARAM_INT) : NULL;
        $stmt->execute();

        $cats = array();
        while (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {               
            $cats[] = $this->rowToCat($row);
        }

        return $cats;
    }

    function getOne(int $id) : Cat {
        $stmt = $this->connection->prepare("SELECT `id`, `breeds`, `description`, `status` FROM `cats` WHERE id=:id LIMIT 1");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $row = $stmt->fetch();
        $cat = $this->rowToCat($row);

        return $cat;
    }

    function insert(Cat $cat) : Cat {
        $stmt = $this->connection->prepare("INSERT INTO `cats`(`breeds`, `description`, `status`) VALUES (:breeds, :description, :status)");
        $stmt->bindParam(":breeds", implode(',', $cat->breeds), PDO::PARAM_STR);
        $stmt->bindParam(":description", $cat->description, PDO::PARAM_STR);
        $stmt->bindParam(":status", $cat->status, PDO::PARAM_INT);
        $stmt->execute();
        return $this->getOne($cat->id);
    }

    function update(Cat $cat, int $id) : Cat {
        if ($id != $cat->id) {
            throw new PDOException("Invalid id!");
        }
        $stmt = $this->connection->prepare("UPDATE `cats` SET `breeds`=:breeds,`description`=:description,`status`=:status WHERE id=:id");
        $stmt->bindParam(":breeds", implode(',', $cat->breeds), PDO::PARAM_STR);
        $stmt->bindParam(":description", $cat->description, PDO::PARAM_STR);
        $stmt->bindParam(":status", $cat->status, PDO::PARAM_INT);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $this->getOne($cat->id);
    }

    function delete(int $id) : bool {
        $stmt = $this->connection->prepare("DELETE FROM `cats` WHERE id=:id LIMIT 1");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    private function rowToCat($row) : Cat {
        return new Cat($row['id'] ?? NULL, isset($row['breeds']) ? explode(',', $row['breeds']) : NULL, $row['description'] ?? NULL, $row['status'] ?? NULL);
    }
}
