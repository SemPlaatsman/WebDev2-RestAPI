<?php

namespace Repositories;

use PDO;
use PDOException;
use Models\User;
use Repositories\Repository;

class UserRepository extends Repository {
    function checkUsernamePassword($username, $password) {
        try {

            // retrieve the user with the given username
            $stmt = $this->connection->prepare("SELECT id, username, password, role, email FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            $stmt->setFetchMode(PDO::FETCH_CLASS, 'Models\User');
            $user = $stmt->fetch();

            if (!$user)
                return false;

            // verify if the password matches the hash in the database
            $result = $this->verifyPassword($password, $user->password);

            if (!$result)
                return false;
            
            // do not pass the password hash to the caller
            $user->password = "";
            return $user;
        } catch (PDOException $e) {
            return false;
        }
    }

    function getAll(int $offset = NULL, int $limit = NULL) : array {
        $query = "SELECT `id`, `username`, `password`, `role`, `email` FROM `users`" . " " . (isset($limit) ? "LIMIT :limit" : "LIMIT 12") . " " . (isset($offset) ? "OFFSET :offset" : "OFFSET 0");
        $stmt = $this->connection->prepare($query);
        isset($limit) ? $stmt->bindParam(':limit', $limit, PDO::PARAM_INT) : NULL;
        isset($offset) ? $stmt->bindParam(':offset', $offset, PDO::PARAM_INT) : NULL;
        $stmt->execute();

        $users = array();
        while (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {               
            $users[] = $this->rowToUser($row);
        }

        return $users;
    }

    function getOne(int $id) : User {
        $stmt = $this->connection->prepare("SELECT `id`, `username`, `password`, `role`, `email` FROM `users` WHERE id=:id LIMIT 1");
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);
        $stmt->execute();

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $row = $stmt->fetch();
        $user = $this->rowToUser($row);

        return $user;
    }

    function register(User $user) : User {
        $user->password = $this->hashPassword($user->password);
        $stmt = $this->connection->prepare("INSERT INTO `users`(`id`, `username`, `password`, `role`, `email`) VALUES (NULL, :username, :password, :role, :email)");
        $stmt->bindParam(':username', $user->username, PDO::PARAM_STR);
        $stmt->bindParam(':password', $user->password, PDO::PARAM_STR);
        $stmt->bindParam(':role', $user->role, PDO::PARAM_INT);
        $stmt->bindParam(':email', $user->email, PDO::PARAM_STR);
        $stmt->execute();
        return $this->getOne($this->connection->lastInsertId());
    }

    // hash the password (currently uses bcrypt)
    private function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    // verify the password hash
    private function verifyPassword($input, $hash) {
        return password_verify($input, $hash);
    }

    private function rowToUser($row) : User {
        return new User($row['id'] ?? NULL, $row['username'] ?? NULL, $row['password'] ?? NULL, $row['role'] ?? NULL, $row['email'] ?? NULL);
    }
}
