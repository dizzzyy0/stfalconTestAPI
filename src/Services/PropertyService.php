<?php
declare(strict_types=1);

namespace App\Services;

use App\DTO\Property\CreatePropertyDTO;
use App\DTO\Property\UpdatePropertyDTO;
use App\Entity\Agent;
use App\Entity\Property;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Uid\Uuid;

class PropertyService
{
    public function __construct(private EntityManagerInterface $entityManager){}

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
        $query = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(Property::class, 'p')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery();
        $paginator = new Paginator($query);
        return[
          'result' => iterator_to_array($paginator),
           'total' => $paginator->count(),
           'offset' => $offset,
           'limit' => $limit,
        ];
    }

    public function getProperty(Uuid $propertyId):Property{
        try{
            return $this->entityManager->getRepository(Property::class)->find($propertyId);
        }catch(\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }

    public function getPropertiesForUser(int $offset, int $limit, $visibleStatuses):array{
        try{
            $query = $this->entityManager->createQueryBuilder()
                ->select('p')
                ->from(Property::class, 'p')
                ->where('p.status IN (:statuses)')
                ->setParameter('statuses', $visibleStatuses)
                ->setFirstResult($offset)
                ->setMaxResults($limit)
                ->getQuery();
            $paginator = new Paginator($query);
            return[
                'result' => iterator_to_array($paginator),
                'total' => $paginator->count(),
                'offset' => $offset,
                'limit' => $limit,
            ];
        }catch(\Exception $e){
            throw new \Exception($e->getMessage());
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

    public function updateProperty(Uuid $propertyId, UpdatePropertyDTO $updatePropertyDTO):Property{
        try{
            $property = $this->entityManager->getRepository(Property::class)->find($propertyId);
            if(!$property){
                throw new \Exception('Property not found');
            }

            $property->applyToEntity($updatePropertyDTO->toEntity());

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
