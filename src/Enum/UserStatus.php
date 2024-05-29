<?php

declare(strict_types=1);

namespace App\Enum;

enum UserStatus: int
{
    use EnumToArrayTrait;

    case AwaitingActivator = 1;

    case Active = 2;

    case Blocked = 3;

}