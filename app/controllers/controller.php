<?php

namespace Controllers;

use Exception;
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

class Controller
{
    function checkForJwt() {
         // Check for token header
         if(!isset($_SERVER['HTTP_AUTHORIZATION'])) {
            // $this->respondWithError("No token provided", 401);
            return;
        }

        // Read JWT from header
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        // Strip the part "Bearer " from the header
        $arr = explode(" ", $authHeader);
        $jwt = $arr[1] ?? NULL;

        // Decode JWT
        $secret_key = "secret123";

        if ($jwt) {
            try {
                $decoded = JWT::decode($jwt, new Key($secret_key, 'HS256'));
                // username is now found in
                // echo $decoded->data->username;
                return $decoded;
            } catch (Exception $e) {
                $this->respondWithError("Unauthorized!", 401);
                return;
            }
        }
    }

    function respond($data, int $httpcode = NULL)
    {
        header('Content-Type: application/json; charset=utf-8');
        if (!isset($httpcode)) {
            $httpcode = 200;
        }

        http_response_code($httpcode);
        echo json_encode($data);
        die();
    }

    function respondWithError($message, int $httpcode)
    {
        $data = array('errorMessage' => $message);
        $this->respond($data, $httpcode);
    }

    function createObjectFromPostedJson($className)
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json);

        $object = new $className();
        foreach ($data as $key => $value) {
            if(is_object($value)) {
                continue;
            }
            $object->{$key} = $value;
        }
        return $object;
    }
}
