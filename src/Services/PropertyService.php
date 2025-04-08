<?php
declare(strict_types=1);

namespace App\Services;

use App\DTO\Property\CreatePropertyDTO;
use App\DTO\Property\UpdatePropertyDTO;
use App\Entity\Agent;
use App\Entity\Property;
use App\Enum\PropertyStatus;
use App\Repository\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

class PropertyService
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly PropertyRepository $propertyRepository
    ){}

    public function createProperty(CreatePropertyDTO $createPropertyDTO):Property{
        try{
            $property = $createPropertyDTO->toEntity();

            $agent = $this->entityManager->getRepository(Agent::class)->find($createPropertyDTO->agentId);
            if(!$agent){
                throw new \Exception('Agent not found');
            }
            $property->setAgent($agent);

            $this->entityManager->persist($property);
            $this->entityManager->flush();

            return $property;
        }catch(\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }

    public function getAllProperties(int $offset, int $limit):array{
        try {
            return $this->propertyRepository->getAllProperties($offset, $limit);
        } catch (\Exception $e) {
            throw new \Exception("Error fetching properties: " . $e->getMessage());
        }
    }

    public function getProperty(Uuid $propertyId):Property{
        try{
            return $this->entityManager->getRepository(Property::class)->find($propertyId);
        }catch(\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }

    public function getPropertiesForUser(int $offset, int $limit, $visibleStatuses):array{
        try {
            return $this->propertyRepository->getPropertiesForUser($offset, $limit, $visibleStatuses);
        } catch (\Exception $e) {
            throw new \Exception("Error fetching properties for user: " . $e->getMessage());
        }
    }

    public function getAgentProperties(Uuid $agentId):array{
        try{
            $agent = $this->entityManager->getRepository(Agent::class)->find($agentId);
            return iterator_to_array($agent->getProperties());
        }catch(\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }

    public function changePropertyStatus(Uuid $propertyId, string $status): Property
    {
        try {
            $property = $this->entityManager->getRepository(Property::class)->find($propertyId);

            if (!$property) {
                throw new \Exception('Property not found');
            }

            $propertyStatus = PropertyStatus::fromId($status);

            if ($propertyStatus === null) {
                throw new \Exception('Invalid property status');
            }

            $property->setStatus($propertyStatus);
            $this->entityManager->flush();

            return $property;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function updateProperty(Uuid $propertyId, UpdatePropertyDTO $updatePropertyDTO):Property{
        try{
            $property = $this->entityManager->getRepository(Property::class)->find($propertyId);
            if(!$property){
                throw new \Exception('Property not found');
            }

            $property = $updatePropertyDTO->toEntity($property);

            $this->entityManager->flush();
            return $property;
        }catch(\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }

    public function deleteProperty(Uuid $propertyId):void{
        $property = $this->entityManager->getRepository(Property::class)->find($propertyId);
        $this->entityManager->remove($property);
        $this->entityManager->flush();
    }
}
