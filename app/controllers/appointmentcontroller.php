<?php

namespace Controllers;

use Exception;
use Models\Appointment;
use Models\Roles;
use Services\AppointmentService;
use Validators\AppointmentValidator;

class AppointmentController extends Controller {
    private $service;
    private ?object $token;

    // initialize services
    function __construct() {
        $this->service = new AppointmentService();

        $token = $this->checkForJwt();
        if (!$token)
            $this->respondWithError("Unauthorized!", 401);

        $this->token = $token;
    }

    public function getAll() {
        try {
            if ($this->token->data->role != Roles::Employee)
                $this->respondWithError("Forbidden!", 403);
            

            $offset = (isset($_GET["offset"]) && is_numeric($_GET["offset"])) ? $_GET["offset"] : NULL;
            $limit = (isset($_GET["limit"]) && is_numeric($_GET["limit"])) ? $_GET["limit"] : NULL;
            
            $appointments = $this->service->getAll($offset, $limit);
            
            $this->respond($appointments);
        } catch(Exception $e) {
            $this->respondWithError("Bad Request!", 400);
        }
    }

    public function getOne(string $id) {
        try {
            $appointment = $this->service->getOne($id);
            
            if ($this->token->data->role != Roles::Employee && $this->token->data->id != $appointment->userId)
                $this->respondWithError("Forbidden!", 403);
            

            // we might need some kind of error checking that returns a 404 if the appointment is not found in the DB
            if (!$appointment)
                $this->respondWithError("Appointment Not Found!", 404);
                
            
            $this->respond($appointment);
        } catch (Exception $e) {
            $this->respondWithError("Bad Request!", 400);
        }
    }

    public function create() {
        try {
            $appointment = $this->createObjectFromPostedJson("Models\\Appointment");

            if(AppointmentValidator::isValid($appointment))
                $this->respondWithError("Invalid Appointment provided!", 400);

            $appointment = $this->service->insert($appointment);
            $this->respond($appointment, 201);
        } catch (Exception $e) {
            $this->respondWithError("Bad Request!", 400);
        }
    }

    public function update(string $id) {
        try {
            if ($this->token->data->role != Roles::Employee)
                $this->respondWithError("Forbidden!", 403);
            

            $appointment = $this->createObjectFromPostedJson("Models\\Appointment");

            if(AppointmentValidator::isValid($appointment))
                $this->respondWithError("Invalid Appointment provided!", 400);

            if ($id != $appointment->id)
                throw new Exception("Invalid id!");

            $appointment = $this->service->update($appointment);
            $this->respond($appointment);
        } catch (Exception $e) {
            $this->respondWithError("Bad Request!", 400);
        }
    }

    public function delete(string $id) {
        try {
            $appointment = $this->service->getOne($id);
            
            if ($this->token->data->role != Roles::Employee && $this->token->data->id != $appointment->userId)
                $this->respondWithError("Forbidden!", 403);
            

            // we might need some kind of error checking that returns a 404 if the appointment is not found in the DB
            if (!$appointment)
                $this->respondWithError("Appointment Not Found!", 404);
            

            if(!$this->service->delete($id))
                throw new Exception("Couldn't delete the appointment!");
            

            $this->respond(true, 204);
        } catch (Exception $e) {
            $this->respondWithError("Bad Request!", 400);
        }
    }
}
