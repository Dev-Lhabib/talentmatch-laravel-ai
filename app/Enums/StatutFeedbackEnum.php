<?php

namespace App\Enums;

enum StatutFeedbackEnum: string
{
    case Nouveau = 'nouveau';
    case Lu = 'lu';
    case Traite = 'traité';
}
