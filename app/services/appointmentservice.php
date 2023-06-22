<?php
namespace Services;

use Models\Appointment;
use Repositories\AppointmentRepository;

class AppointmentService {

    private $appointmentRepository;

    function __construct()
    {
        $this->appointmentRepository = new AppointmentRepository();
    }

    public function getAll($offset = NULL, $limit = NULL) : array {
        return $this->appointmentRepository->getAll($offset, $limit);
    }

    public function getOne(string $id) : ?Appointment {
        return $this->appointmentRepository->getOne($id);
    }

    public function insert(Appointment $appointment) : ?Appointment {       
        return $this->appointmentRepository->insert($appointment);        
    }

    public function update(Appointment $appointment) : ?Appointment {       
        return $this->appointmentRepository->update($appointment);        
    }

    public function delete(string $id) : bool {       
        return $this->appointmentRepository->delete($id);
    }
}

?>