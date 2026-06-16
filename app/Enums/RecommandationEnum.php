<?php

namespace App\Enums;

enum RecommandationEnum: string
{
    case Convoquer = 'convoquer';
    case Attente = 'attente';
    case Rejeter = 'rejeter';
}
