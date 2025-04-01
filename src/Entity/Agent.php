<?php

namespace App\Entity;

use AllowDynamicProperties;
use App\Repository\AgentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[AllowDynamicProperties] #[ORM\Entity(repositoryClass: AgentRepository::class)]
class Agent extends User
{
    #[ORM\OneToMany(targetEntity: Property::class, mappedBy: 'agent')]
    private Collection $properties;

    public function __construct()
    {
        parent::__construct();
        $this->roles = ["ROLE_AGENT"];
    }

    public function getProperties(): Collection
    {
        return $this->properties;
    }

    public function setProperties(Collection $properties): static
    {
        $this->properties = $properties;
        return $this;
    }
}
