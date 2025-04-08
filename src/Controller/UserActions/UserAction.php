<?php
declare(strict_types=1);

namespace App\Controller\UserActions;

use App\Presenters\UserPresenter;
use App\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class UserAction extends AbstractController
{
    public function __construct(
        protected readonly UserService $userService,
        protected readonly UserPresenter $userPresenter,
    )
    { }
}
