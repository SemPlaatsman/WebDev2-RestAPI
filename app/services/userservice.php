<?php
namespace Services;

use Models\User;
use Repositories\UserRepository;
use Repositories\AppointmentRepository;
use Repositories\CatRepository;

class UserService {

    private $userRepository;
    private $appointmentRepository;
    private $catRepository;

    function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->appointmentRepository = new AppointmentRepository();
        $this->catRepository = new CatRepository();
    }

    public function checkUsernamePassword($username, $password) {
        return $this->userRepository->checkUsernamePassword($username, $password);
    }

    public function getAll($offset = NULL, $limit = NULL) : array {
        return $this->userRepository->getAll($offset, $limit);
    }

    public function getOne($id) : ?User {
        return $this->userRepository->getOne($id);
    }

    public function getAppointments(int $id) : array {
        return $this->appointmentRepository->getAllOfUser($id);
    }

    public function getCats(int $id) : array {
        return $this->catRepository->getAllOfUser($id);
    }

    public function create(User $user) : ?User {
        return $this->userRepository->create($user);
    }

    public function update(User $user) : ?User {
        return $this->userRepository->update($user);
    }

    public function delete(int $id) : bool {
        return $this->userRepository->delete($id);
    }
}

?>