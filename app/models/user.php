<?php
namespace Models;

class User {

    public int $id;
    public string $username;
    public string $password;
    public int $role;
    public string $email;

    function __construct(string $id = NULL, string $username = NULL, string $password = NULL, int $role = NULL, string $email = NULL) {
        if (isset($id)) $this->id = $id;
        if (isset($username)) $this->username = $username;
        if (isset($password)) $this->password = $password;
        if (isset($role)) $this->role = $role;
        if (isset($email)) $this->email = $email;
    }
}

?>