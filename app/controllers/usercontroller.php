<?php

namespace Controllers;

use Exception;
use Models\Roles;
use Services\UserService;
use Validators\UserValidator;
use \Firebase\JWT\JWT;
use Models\User;

class UserController extends Controller {
    private $service;

    // initialize services
    function __construct() {
        $this->service = new UserService();
    }

    public function getAll() {
        try {
            $token = $this->checkForJwt();

            if (!$token)
                $this->respondWithError("Unauthorized!", 401);

            if ($token->data->role != Roles::Employee)
                $this->respondWithError("Forbidden!", 403);

            $offset = (isset($_GET["offset"]) && is_numeric($_GET["offset"])) ? $_GET["offset"] : NULL;
            $limit = (isset($_GET["limit"]) && is_numeric($_GET["limit"])) ? $_GET["limit"] : NULL;

            $users = $this->service->getAll($offset, $limit);
            
            $this->respond($users);
        } catch(Exception $e) {
            $this->respondWithError("Bad Request!", 400);
        }
    }

    public function getOne(int $id) {
        try {
            $token = $this->checkForJwt();

            if (!$token)
                $this->respondWithError("Unauthorized!", 401);

            $user = $this->service->getOne($id);

            if ($token->data->role != Roles::Employee && $user->id != $token->data->id)
                $this->respondWithError("Forbidden!", 403);
            
            // we might need some kind of error checking that returns a 404 if the user is not found in the DB
            if (!$user)
                $this->respondWithError("User Not Found!", 404);
            $this->respond($user);
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

            $user = $this->createObjectFromPostedJson("Models\\User");

            if(UserValidator::isValid($user))
                $this->respondWithError("Invalid User provided!", 400);

            if ($id != $user->id) 
                throw new Exception("Invalid id!");
            
            $user = $this->service->update($user, $id);
            $this->respond($user);
        } catch (Exception $e) {
            $this->respondWithError("Bad Request!", 400);
        }
    }

    public function delete(int $id) {
        try {
            $token = $this->checkForJwt();

            if (!$token)
                $this->respondWithError("Unauthorized!", 401);

            $user = $this->service->getOne($id);
            
            // we might need some kind of error checking that returns a 404 if the user is not found in the DB
            if (!$user)
                $this->respondWithError("User Not Found!", 404);

            if ($token->data->role != Roles::Employee && $token->data->id != $user->id)
                $this->respondWithError("Forbidden!", 403);

            if(!$this->service->delete($id))
                throw new Exception("Couldn't delete the user!");
            
            $this->respond(true, 204);
        } catch (Exception $e) {
            $this->respondWithError("Bad Request!", 400);
        }
    }

    public function login() {
        try {
            // read user data from request body
            $postedUser = $this->createObjectFromPostedJson("Models\\User");
    
            // get user from db
            $user = $this->service->checkUsernamePassword($postedUser->username, $postedUser->password);
    
            // if the method returned false, the username and/or password were incorrect
            if(!$user) {
                $this->respondWithError("Invalid username/password combination!", 401);
                return;
            }
    
            // generate jwt
            $tokenResponse = $this->generateJwt($user);       
    
            $this->respond($tokenResponse);
        } catch (Exception $e) {
            $this->respondWithError("Bad Request!", 400);
        }
    }

    public function register() {
        try {
            $token = $this->checkForJwt();
                
            $user = $this->createObjectFromPostedJson("Models\\User");

            if(UserValidator::isValid($user))
                $this->respondWithError("Invalid User provided!", 400);

            if (($token && $token->data->role != Roles::Employee ) && $user->role == Roles::Employee)
                $this->respondWithError("Forbidden!", 403);
            
            $user = $this->service->create($user);
            unset($user->password);
            $this->respond($user, 201);
        } catch (Exception $e) {
            $this->respondWithError("Bad Request!", 400);
        }
    }

    public function generateJwt($user) {
        $secret_key = "secret123";

        $issuer = "localhost:5173"; // this can be the domain/servername that issues the token
        $audience = "localhost"; // this can be the domain/servername that checks the token

        $issuedAt = time(); // issued at
        $notbefore = $issuedAt; //not valid before 
        // TODO: CHANGE EXPIRE TIME TO 600 SECONDS!!!
        $expire = $issuedAt + 7200; // expiration time is set at +600 seconds (10 minutes)

        // JWT expiration times should be kept short (10-30 minutes)
        // A refresh token system should be implemented if we want clients to stay logged in for longer periods

        // note how these claims are 3 characters long to keep the JWT as small as possible
        $payload = array(
            "iss" => $issuer,
            "aud" => $audience,
            "iat" => $issuedAt,
            "nbf" => $notbefore,
            "exp" => $expire,
            "data" => array(
                "id" => $user->id,
                "username" => $user->username,
                "email" => $user->email,
                "role" => $user->role
        ));

        $jwt = JWT::encode($payload, $secret_key, 'HS256');

        return 
            array(
                "message" => "Successful login.",
                "jwt" => $jwt,
                "username" => $user->username,
                "email" => $user->email,
                "role" => $user->role,
                "expireAt" => $expire
            );
    }    
}
