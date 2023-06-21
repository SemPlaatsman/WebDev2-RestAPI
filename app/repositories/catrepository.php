<?php

namespace Repositories;

use Exception;
use Models\Cat;
use PDO;
use PDOException;
use Repositories\Repository;

class CatRepository extends Repository {
    function getAll(int $status = NULL, int $offset = NULL, int $limit = NULL) : array {
        $query = "SELECT `id`, `user_id`, `image`, `image_format`, `breeds`, `description`, `status` FROM `cats`" . (isset($status) ? " WHERE `status`=:status" : "") . 
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
        $stmt = $this->connection->prepare("SELECT `id`, `user_id`, `image`, `image_format`, `breeds`, `description`, `status` FROM `cats` WHERE id=:id LIMIT 1");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $row = $stmt->fetch();
        $cat = $this->rowToCat($row);

        return $cat;
    }

    function insert(Cat $cat) : Cat {
        $imageBlob = base64_decode($cat->encodedImage);
        $implodedBreeds = implode(',', $cat->breeds);
        $stmt = $this->connection->prepare("INSERT INTO `cats`(`user_id`, `image`, `image_format`, `breeds`, `description`, `status`) VALUES (:user_id, :image, :image_format, :breeds, :description, :status)");
        $stmt->bindParam(":user_id", $cat->userId, PDO::PARAM_INT);
        $stmt->bindParam(":image", $imageBlob, PDO::PARAM_LOB);
        $stmt->bindParam("image_format", $cat->imageFormat, PDO::PARAM_STR);
        $stmt->bindParam(":breeds", $implodedBreeds, PDO::PARAM_STR);
        $stmt->bindParam(":description", $cat->description, PDO::PARAM_STR);
        $stmt->bindParam(":status", $cat->status, PDO::PARAM_INT);
        $stmt->execute();
        return $this->getOne($this->connection->lastInsertId());
    }

    function update(Cat $cat) : Cat {
        $stmt = $this->connection->prepare("UPDATE `cats` SET `user_id`=:user_id, `image`=:image, `image_format`=:image_format, `breeds`=:breeds, `description`=:description, `status`=:status WHERE id=:id");
        $stmt->bindParam(":user_id", $cat->userId, PDO::PARAM_INT);
        $stmt->bindParam(":image", base64_encode($cat->encodedImage), PDO::PARAM_LOB);
        $stmt->bindParam("image_format", $cat->imageFormat, PDO::PARAM_STR);
        $stmt->bindParam(":breeds", implode(',', $cat->breeds), PDO::PARAM_STR);
        $stmt->bindParam(":description", $cat->description, PDO::PARAM_STR);
        $stmt->bindParam(":status", $cat->status, PDO::PARAM_INT);
        $stmt->bindParam(":id", $cat->id, PDO::PARAM_INT);
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
        return new Cat($row['id'] ?? NULL, $row['user_id'] ?? NULL, base64_encode($row['image'] ?? ""), $row['image_format'] ?? NULL, isset($row['breeds']) ? explode(',', $row['breeds']) : NULL, $row['description'] ?? NULL, $row['status'] ?? NULL);
    }
}
