<?php
namespace Services;

use Models\Appointment;
use Repositories\AppointmentRepository;

class AppointmentService {

    private $repository;

    function __construct()
    {
        $this->repository = new AppointmentRepository();
    }

    public function getAll($offset = NULL, $limit = NULL) : array {
        return $this->repository->getAll($offset, $limit);
    }

    public function getOne(string $id) : Appointment {
        return $this->repository->getOne($id);
    }

    public function insert(Appointment $appointment) : Appointment {       
        return $this->repository->insert($appointment);        
    }

    public function update(Appointment $appointment) : Appointment {       
        return $this->repository->update($appointment);        
    }

    public function delete(string $id) : bool {       
        return $this->repository->delete($id);
    }
}

?>