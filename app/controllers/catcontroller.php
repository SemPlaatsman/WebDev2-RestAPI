<?php

namespace Controllers;

use COM;
use Models\CatStatus;

use Exception;
use Services\CatService;

class CatController extends Controller {
    private $service;
    private object $token;

    // initialize services
    function __construct() {
        $this->service = new CatService();

        $token = $this->checkForJwt();
        if (!$token)
            die();

        $this->token = $token;
    }

    public function getAll() {
        try {
            $status = (isset($_GET["status"]) && is_numeric($_GET["status"])) ? $_GET["status"] : NULL;
            $offset = (isset($_GET["offset"]) && is_numeric($_GET["offset"])) ? $_GET["offset"] : NULL;
            $limit = (isset($_GET["limit"]) && is_numeric($_GET["limit"])) ? $_GET["limit"] : NULL;
            
            $cat = $this->service->getAll($status, $offset, $limit);
            
            $this->respond($cat);
        } catch (Exception $e) {
            $this->respondWithError("Bad Request!", 400);
        }
    }

    // public function getAllFound() {
    //     try {
    //         $this->getAll(CatStatus::Found);
    //     } catch (Exception $e) {
    //         $this->respondWithError("Bad Request!", 400);
    //     }
    // }

    // public function getAllLost() {
    //     try {
    //         $this->getAll(CatStatus::Lost);
    //     } catch (Exception $e) {
    //         $this->respondWithError("Bad Request!", 400);
    //     }
    // }

    // public function getAllInShelter() {
    //     try {
    //         $this->getAll(CatStatus::InShelter);
    //     } catch (Exception $e) {
    //         $this->respondWithError("Bad Request!", 400);
    //     }
    // }

    public function getOne(int $id) {
        try {
            $cat = $this->service->getOne($id);
            
            // we might need some kind of error checking that returns a 404 if the cat is not found in the DB
            if (!$cat) {
                $this->respondWithError("Cat not found", 404);
                return;
            }
            
            $this->respond($cat);
        } catch (Exception $e) {
            $this->respondWithError("Bad Request!", 400);
        }
    }

    public function create() {
        try {
            $cat = $this->createObjectFromPostedJson("Models\\Cat");
            $cat = $this->service->insert($cat);
            $this->respond($cat, 201);
        } catch (Exception $e) {
            $this->respondWithError("Bad Request!", 400);
        }
    }

    public function update(int $id) {
        try {
            $cat = $this->createObjectFromPostedJson("Models\\Cat");
            $cat = $this->service->update($cat, $id);
            $this->respond($cat);
        } catch (Exception $e) {
            $this->respondWithError("Bad Request!", 400);
        }
    }

    public function delete(int $id) {
        try {
            if(!$this->service->delete($id)) {
                throw new Exception("Couldn't delete the cat record!");
            }

            $this->respond(true, 204);
        } catch (Exception $e) {
            $this->respondWithError("Bad Request!", 400);
        }
    }
}
