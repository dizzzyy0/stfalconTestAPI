<?php
declare(strict_types=1);

namespace App\Services;

use App\DTO\User\RegisterUserDTO;
use App\DTO\User\UpdateUserDTO;
use App\Entity\Agent;
use App\Entity\Customer;
use App\Entity\Property;
use App\Entity\User;
use App\Enum\Role;
use App\Repository\PropertyRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Uid\Uuid;

class UserService
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UserRepository $userRepository,
    )
    {}

    public function register(RegisterUserDTO $registerUserDTO):User{
        $existingUser = $this->userRepository->findOneBy(["email"=> $registerUserDTO->email]);
        if($existingUser){
            throw new \Exception(sprintf('User with email %s already exists', $registerUserDTO->email));
        }

        $role = Role::tryFrom($registerUserDTO->role);

        if (!$role) {
            throw new \Exception('Invalid role');
        }

        $user = match ($role) {
            Role::CUSTOMER => new Customer(),
            Role::AGENT => new Agent(),
            default => throw new \Exception('Unsupported role'),
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
        try {
            return $this->userRepository->getAllUsers($offset, $limit);
        } catch (\Exception $e) {
            throw new \Exception("Error fetching users: " . $e->getMessage());
        }
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

    public function addFavoriteProperty(Uuid $userId, Uuid $propertyId): Customer
    {
        $customer = $this->entityManager->getRepository(Customer::class)->find($userId);

        if (!$customer) {
            throw new \InvalidArgumentException('Customer not found with ID: ' . $userId);
        }

        $property = $this->entityManager->getRepository(Property::class)->find($propertyId);

        if (!$property) {
            throw new \InvalidArgumentException('Property not found with ID: ' . $propertyId);
        }

        $customer->addFavoriteProperty($property);
        $this->entityManager->persist($customer);
        $this->entityManager->flush();

        return $customer;
    }

    public function removeFavoriteProperty(Uuid $userId, Uuid $propertyId): Customer{
        $customer = $this->entityManager->getRepository(Customer::class)->find($userId);
        if (!$customer) {
            throw new \InvalidArgumentException('Customer not found with ID: ' . $userId);
        }

        $property = $this->entityManager->getRepository(Property::class)->find($propertyId);

        if (!$property) {
            throw new \InvalidArgumentException('Property not found with ID: ' . $propertyId);
        }

        $customer->removeFavoriteProperty($property);
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
