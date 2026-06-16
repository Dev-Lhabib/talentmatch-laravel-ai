<?php

namespace App\Enums;

enum MessageRoleEnum: string
{
    case User = 'user';
    case Assistant = 'assistant';
    case Tool = 'tool';
}
