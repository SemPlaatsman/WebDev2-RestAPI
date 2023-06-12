<?php
namespace Services;

use Models\User;
use Repositories\UserRepository;

class UserService {

    private $repository;

    function __construct()
    {
        $this->repository = new UserRepository();
    }

    public function checkUsernamePassword($username, $password) {
        return $this->repository->checkUsernamePassword($username, $password);
    }

    public function getAll($offset = NULL, $limit = NULL) : array {
        return $this->repository->getAll($offset, $limit);
    }

    public function getOne($id) : User {
        return $this->repository->getOne($id);
    }

    public function register(User $user) : User {
        return $this->repository->register($user);
    }
}

?>