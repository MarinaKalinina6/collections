<?php

declare(strict_types=1);

namespace App\Enum;

enum CustomAttributeType: string
{
    case Integer = 'INI';
    case String = 'STRING';
    case Boolean = 'BOOLEAN';
    case Text = 'TEXT';
    case Date = 'DATE';
}
