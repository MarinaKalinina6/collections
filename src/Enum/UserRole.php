<?php

declare(strict_types=1);

namespace App\Enum;

enum UserRole: string
{
    use EnumToArrayTrait;

    case User = 'ROLE_USER';

    case Admin = 'ROLE_ADMIN';


}