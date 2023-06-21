<?php
namespace Models;

class Cat {
    public int $id;
    public int $userId;
    public string $imageFormat;
    public array $breeds;
    public string $description;
    public int $status;

    function __construct(int $id, int $userId, string $imageFormat, array $breeds, string $description, int $status) {
        if (isset($id)) $this->id = $id;
        if (isset($userId)) $this->userId = $userId;
        if (isset($imageFormat)) $this->imageFormat = $imageFormat;
        if (isset($breeds)) $this->breeds = $breeds;
        if (isset($description)) $this->description = $description;
        if (isset($status)) $this->status = $status;
    }
}

?>