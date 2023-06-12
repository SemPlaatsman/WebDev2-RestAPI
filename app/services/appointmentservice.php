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

    public function getOne($id) : Appointment {
        return $this->repository->getOne($id);
    }

    public function insert($appointment) : Appointment {       
        return $this->repository->insert($appointment);        
    }

    public function update($appointment, $id) : Appointment {       
        return $this->repository->update($appointment, $id);        
    }

    public function delete($id) : bool {       
        return $this->repository->delete($id);
    }
}

?>