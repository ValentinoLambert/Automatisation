<?php

namespace controller\Departement;

use model\Departement\Departement;

class GetDepartment {

    protected $departments = array();

    public function getAllDepartments() {
        return Departement::orderBy('nom_departement')->get()->toArray();
    }
}
