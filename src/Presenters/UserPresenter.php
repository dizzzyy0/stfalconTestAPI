<?php
declare(strict_types=1);

namespace App\Presenters;

use App\Entity\Customer;
use App\Entity\User;


final readonly class UserPresenter
{
    public function __construct(
        private readonly PropertyPresenter $propertyPresenter,
    ){}
    public function present(User $user): array
    {
        return [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'phone' => $user->getPhone(),
            'roles' => $user->getRoles(),
        ];
    }

    public function presentPaginatedUser(array $paginationData): array{
        return [
            'result' => array_map(fn (User $user) => $this->present($user), $paginationData['result']),
            'metadata' =>[
                'total' => $paginationData['total'],
                'offset' => $paginationData['offset'],
                'limit' => $paginationData['limit'],
            ],
        ];
    }

    public function presentCustomer(Customer $customer): array {
        $result = $this->present($customer);
        $result['favorites'] = $this->propertyPresenter->presentList($customer->getFavoriteProperties());
        return $result;
    }
}
