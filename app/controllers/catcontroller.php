<?php

namespace Controllers;

use COM;
use Models\Roles;
use Models\CatStatus;

use Exception;
use Services\CatService;
use Validators\CatValidator;

class CatController extends Controller {
    private $service;

    // initialize services
    function __construct() {
        $this->service = new CatService();
    }

    public function getAll() {
        try {
            $status = (isset($_GET["status"]) && is_numeric($_GET["status"])) ? $_GET["status"] : NULL;
            $offset = (isset($_GET["offset"]) && is_numeric($_GET["offset"])) ? $_GET["offset"] : NULL;
            $limit = (isset($_GET["limit"]) && is_numeric($_GET["limit"])) ? $_GET["limit"] : NULL;
            
            $cats = $this->service->getAll($status, $offset, $limit);
            
            $this->respond($cats);
        } catch (Exception $e) {
            $this->respondWithError("Bad Request!", 400);
        }
    }

    public function getOne(int $id) {
        try {
            $cat = $this->service->getOne($id);
            
            // we might need some kind of error checking that returns a 404 if the cat is not found in the DB
            if (!$cat)
                $this->respondWithError("Cat Not Found!", 404);
            
            $this->respond($cat);
        } catch (Exception $e) {
            $this->respondWithError("Bad Request!", 400);
        }
    }

    public function create() {
        try {
            $token = $this->checkForJwt();

            if (!$token)
                $this->respondWithError("Unauthorized!", 401);

            $cat = $this->createObjectFromPostedJson("Models\\Cat");

            if(CatValidator::isValid($cat))
                $this->respondWithError("Invalid Cat provided!", 400);

            $cat = $this->service->insert($cat);
            $this->respond($cat, 201);
        } catch (Exception $e) {
            $this->respondWithError("Bad Request!", 400);
        }
    }

    public function update(int $id) {
        try {
            $token = $this->checkForJwt();

            if (!$token)
                $this->respondWithError("Unauthorized!", 401);

            if ($token->data->role != Roles::Employee)
                $this->respondWithError("Forbidden!", 403);

            $cat = $this->createObjectFromPostedJson("Models\\Cat");

            if(CatValidator::isValid($cat))
                $this->respondWithError("Invalid Cat provided!", 400);

            if ($id != $cat->id) 
                throw new Exception("Invalid id!");
            
            $cat = $this->service->update($cat);
            $this->respond($cat);
        } catch (Exception $e) {
            $this->respondWithError("Bad Request!", 400);
        }
    }

    public function delete(int $id) {
        try {
            $token = $this->checkForJwt();

            if (!$token)
                $this->respondWithError("Unauthorized!", 401);

            $cat = $this->service->getOne($id);
            
            // we might need some kind of error checking that returns a 404 if the cat is not found in the DB
            if (!$cat)
                $this->respondWithError("Cat Not Found!", 404);

            if ($token->data->role != Roles::Employee && $token->data->id != $cat->userId)
                $this->respondWithError("Forbidden!", 403);

            if(!$this->service->delete($id))
                throw new Exception("Couldn't delete the cat!");
            

            $this->respond(true, 204);
        } catch (Exception $e) {
            $this->respondWithError("Bad Request!", 400);
        }
    }
}
