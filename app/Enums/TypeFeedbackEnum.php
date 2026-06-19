<?php

namespace App\Enums;

enum TypeFeedbackEnum: string
{
    case Bug = 'bug';
    case Suggestion = 'suggestion';
    case Analyse = 'analyse';
    case Autre = 'autre';
}
