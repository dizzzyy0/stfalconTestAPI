<?php
declare(strict_types=1);

namespace App\Presenters;

use App\Entity\Customer;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

final readonly class UserPresenter
{
    public function __construct(private readonly EntityManagerInterface $entityManager){}
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

    public function presentCustomer(Customer $customer): array{
        $propertyTypePresenter = new PropertyTypesPresenter();
        $propertyStatusPresenter = new PropertyStatusPresenter();
        $currencyPresenter = new CurrencyPresenter();
        $pricePresenter = new PricePresenter($currencyPresenter);
        $locationPresenter = new LocationPresenter();
        $sizePresenter = new SizePresenter();
        $propertyPresenter = new PropertyPresenter();

        $result = $this->present($customer);
        $result['favorites'] = $propertyPresenter->presentProperties($customer->getFavoriteProperties());
        return $result;
    }
}
