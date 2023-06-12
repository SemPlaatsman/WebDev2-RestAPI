<?php

namespace Controllers;

use Exception;
use Models\Roles;
use Services\UserService;
use \Firebase\JWT\JWT;

class UserController extends Controller {
    private $service;

    // initialize services
    function __construct() {
        $this->service = new UserService();
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

    public function getAll() {
        try {
            $offset = NULL;
            $limit = NULL;

            (isset($_GET["offset"]) && is_numeric($_GET["offset"])) ? $offset = $_GET["offset"] : NULL;
            (isset($_GET["limit"]) && is_numeric($_GET["limit"])) ? $limit = $_GET["limit"] : NULL;
            
            $users = $this->service->getAll($offset, $limit);
            
            $this->respond($users);
        } catch(Exception $e) {
            $this->respondWithError("Bad Request!", 400);
        }
    }

    public function getOne(int $id) {
        try {
            $user = $this->service->getOne($id);
            
            // we might need some kind of error checking that returns a 404 if the user is not found in the DB
            if (!$user) {
                $this->respondWithError("User not found", 404);
                return;
            }
            
            $this->respond($user);
        } catch (Exception $e) {
            $this->respondWithError("Bad Request!", 400);
        }
    }

    public function register() {
        try {
            $token = $this->checkForJwt();
                
            $user = $this->createObjectFromPostedJson("Models\\User");

            if (!$token && $user->role == Roles::Employee) {
                $this->respondWithError("Invalid credentials provided!", 403);
                return;
            }

            $user = $this->service->register($user);
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
