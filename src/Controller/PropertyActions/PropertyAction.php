<?php
declare(strict_types=1);

namespace App\Controller\PropertyActions;

use App\Presenters\PropertyPresenter;
use App\Services\PropertyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class PropertyAction extends AbstractController
{
    public function __construct(
        protected readonly PropertyService $propertyService,
        protected readonly PropertyPresenter $propertyPresenter,
    )
    {}
}
