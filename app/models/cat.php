<?php
namespace Models;

class Cat {
    public int $id;
    public array $breeds;
    public string $description;
    public int $status;

    function __construct(int $id, array $breeds, string $description) {
        if (isset($id)) $this->id = $id;
        if (isset($breeds)) $this->breeds = $breeds;
        if (isset($description)) $this->description = $description;
        if (isset($status)) $this->status = $status;
    }
}

?>