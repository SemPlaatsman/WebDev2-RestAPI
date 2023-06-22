<?php
namespace Services;

use Models\Cat;

use Repositories\CatRepository;

class CatService {
    private $catRepository;

    function __construct() {
        $this->catRepository = new CatRepository();
    }

    public function getAll(int $status = NULL, $offset = NULL, $limit = NULL) : array {
        return $this->catRepository->getAll($status, $offset, $limit);
    }

    public function getOne(int $id) : ?Cat {
        return $this->catRepository->getOne($id);
    }

    public function insert(Cat $cat) : ?Cat {       
        return $this->catRepository->insert($cat);        
    }

    public function update(Cat $cat) : ?Cat {       
        return $this->catRepository->update($cat);
    }

    public function delete(int $id) : bool {
        return $this->catRepository->delete($id);
    }
}

?>