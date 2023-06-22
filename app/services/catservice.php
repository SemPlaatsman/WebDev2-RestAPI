<?php
namespace Services;

use Models\Cat;

use Repositories\CatRepository;

class CatService {
    private $repository;

    function __construct() {
        $this->repository = new CatRepository();
    }

    public function getAll(int $status = NULL, $offset = NULL, $limit = NULL) : array {
        return $this->repository->getAll($status, $offset, $limit);
    }

    public function getOne(int $id) : ?Cat {
        return $this->repository->getOne($id);
    }

    public function insert(Cat $cat) : ?Cat {       
        return $this->repository->insert($cat);        
    }

    public function update(Cat $cat) : ?Cat {       
        return $this->repository->update($cat);
    }

    public function delete(int $id) : bool {
        return $this->repository->delete($id);
    }
}

?>