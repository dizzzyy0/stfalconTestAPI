<?php
declare(strict_types=1);
namespace App\Enum;

enum Role: string
{
    case ADMIN = 'ROLE_ADMIN';
    case CUSTOMER = 'ROLE_CUSTOMER';
    case AGENT = 'ROLE_AGENT';

}
