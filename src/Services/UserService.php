<?php
declare(strict_types=1);

namespace App\Services;

use App\DTO\User\RegisterUserDTO;
use App\DTO\User\UpdateUserDTO;
use App\Entity\Agent;
use App\Entity\Customer;
use App\Entity\Property;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Uid\Uuid;

class UserService
{

    public function __construct(private EntityManagerInterface $entityManager, private UserPasswordHasherInterface $passwordHasher)
    {}

    public function register(RegisterUserDTO $registerUserDTO):User{
        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $registerUserDTO->email]);
        if($existingUser){
            return new Customer();
        }

        $user = match ($registerUserDTO->role){
            'ROLE_CUSTOMER' => new Customer(),
            'ROLE_AGENT' => new Agent(),
            default => throw new \Exception('Invalid role'),
        };

        $user->setEmail($registerUserDTO->email);
        $user->setName($registerUserDTO->name);
        $user->setPhone($registerUserDTO->phone);
        $user->setPassword($this->passwordHasher->hashPassword($user, $registerUserDTO->password));
        $user->setIsBlocked(false);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return $user;
    }

    public function blockUser(Uuid $userId): void{
        $user = $this->entityManager->getRepository(User::class)->find($userId);
        $user->setIsBlocked(true);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function unblockUser(Uuid $userId): void{
        $user = $this->entityManager->getRepository(User::class)->find($userId);
        $user->setIsBlocked(false);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
    public function getById(Uuid $userId): User{
        return $this->entityManager->getRepository(User::class)->find($userId);
    }

    public function getAllUsers(int $offset, int $limit): array{
        $query = $this->entityManager->getRepository(User::class)->createQueryBuilder('u')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery();
        $paginator = new Paginator($query);
        return [
            'result'=> iterator_to_array($paginator),
            'total' => $paginator->count(),
            'offset' => $offset,
            'limit' => $limit,
        ];
    }

    public function updateUser(Uuid $userId, UpdateUserDTO $newUser): User{
        $existingUser = $this->entityManager->getRepository(User::class)->find($userId);
        if($newUser->name){
            $existingUser->setName($newUser->name);
        }
        if($newUser->phone){
            $existingUser->setPhone($newUser->phone);
        }
        if($newUser->email){
            $existingUser->setEmail($newUser->email);
        }
        if($newUser->password){
            $existingUser->setPassword($this->passwordHasher->hashPassword($existingUser, $newUser->password));
        }
        $this->entityManager->persist($existingUser);
        $this->entityManager->flush();
        return $existingUser;
    }

    public function addFavoriteProperty(Uuid $userId, Uuid $propertyId): Customer{
        $customer = $this->entityManager->getRepository(Customer::class)->find($userId);
        $propertyId = $this->entityManager->getRepository(Property::class)->find($propertyId);
        $customer->addFavoriteProperty($propertyId);
        $this->entityManager->persist($customer);
        $this->entityManager->flush();
        return $customer;
    }

    public function removeFavoriteProperty(Uuid $userId, Uuid $propertyId): Customer{
        $customer = $this->entityManager->getRepository(Customer::class)->find($userId);
        $propertyId = $this->entityManager->getRepository(Property::class)->find($propertyId);
        $customer->removeFavoriteProperty($propertyId);
        $this->entityManager->persist($customer);
        $this->entityManager->flush();
        return $customer;
    }
    public function deleteUser(Uuid $userId): void{
        $user = $this->entityManager->getRepository(User::class)->find($userId);
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }
}
