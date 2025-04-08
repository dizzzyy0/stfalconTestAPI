<?php

namespace App\Entity;

use App\Enum\Role;
use App\Repository\AdminRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdminRepository::class)]
class Admin extends User
{
    public function __construct()
    {
        parent::__construct();
        $this->roles = [Role::ADMIN->value];
    }
}
