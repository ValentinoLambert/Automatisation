<?php

namespace controller\Departement;

use model\Departement\Departement;

class GetDepartment
{
    protected array $departments = [];

    public function getAllDepartments(): array
    {
        return Departement::orderBy('nom_departement')->get()->toArray();
    }
}
