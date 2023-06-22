<?php
namespace Models;

class Cat {
    public int $id;
    public int $userId;
    public string $userEmail;
    public string $encodedImage;
    public string $imageFormat;
    public array $breeds;
    public string $description;
    public int $status;

    function __construct(int $id = NULL, int $userId = NULL, string $userEmail = NULL, $encodedImage = NULL, string $imageFormat = NULL, array $breeds = NULL, string $description = NULL, int $status = NULL) {
        if (isset($id)) $this->id = $id;
        if (isset($userId)) $this->userId = $userId;
        if (isset($userEmail)) $this->userEmail = $userEmail;
        if (isset($encodedImage)) $this->encodedImage = $encodedImage;
        if (isset($imageFormat)) $this->imageFormat = $imageFormat;
        if (isset($breeds)) $this->breeds = $breeds;
        if (isset($description)) $this->description = $description;
        if (isset($status)) $this->status = $status;
    }
}

?>