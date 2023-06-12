<?php

namespace Controllers;

use Exception;
use Services\AppointmentService;

class AppointmentController extends Controller {
    private $service;
    private object $token;

    // initialize services
    function __construct() {
        $this->service = new AppointmentService();

        $token = $this->checkForJwt();
        if (!$token)
            die();

        $this->token = $token;
    }

    public function getAll() {
        try {
            $offset = NULL;
            $limit = NULL;

            (isset($_GET["offset"]) && is_numeric($_GET["offset"])) ? $offset = $_GET["offset"] : NULL;
            (isset($_GET["limit"]) && is_numeric($_GET["limit"])) ? $limit = $_GET["limit"] : NULL;
            
            $appointments = $this->service->getAll($offset, $limit);
            
            $this->respond($appointments);
        } catch(Exception $e) {
            $this->respondWithError("Bad Request!", 400);
        }
    }

    public function getOne(string $id) {
        try {
            $appointment = $this->service->getOne($id);
            
            // we might need some kind of error checking that returns a 404 if the appointment is not found in the DB
            if (!$appointment) {
                $this->respondWithError("Appointment not found", 404);
                return;
            }
            
            $this->respond($appointment);
        } catch (Exception $e) {
            $this->respondWithError("Bad Request!", 400);
        }
    }

    public function create() {
        try {
            $appointment = $this->createObjectFromPostedJson("Models\\Appointment");
            $appointment = $this->service->insert($appointment);
            $this->respond($appointment, 201);
        } catch (Exception $e) {
            $this->respondWithError("Bad Request!", 400);
        }
    }

    public function update(string $id) {
        try {
            $appointment = $this->createObjectFromPostedJson("Models\\Appointment");
            $appointment = $this->service->update($appointment, $id);
            $this->respond($appointment);
        } catch (Exception $e) {
            $this->respondWithError("Bad Request!", 400);
        }
    }

    public function delete(string $id) {
        try {
            if(!$this->service->delete($id)) {
                throw new Exception("Couldn't delete the appointment!");
            }

            $this->respond(true, 204);
        } catch (Exception $e) {
            $this->respondWithError("Bad Request!", 400);
        }
    }
}
