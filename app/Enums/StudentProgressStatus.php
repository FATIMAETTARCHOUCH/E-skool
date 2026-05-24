<?php

namespace App\Enums;

enum StudentProgressStatus: string
{
    case LOCKED = 'locked';
    case UNLOCKED = 'unlocked';
    case IN_PROGRESS = 'in_progress';
    case IN_REMEDIATION = 'in_remediation';
    case PASSED = 'passed';
    case PASSED_WITH_HELP = 'passed_with_help';
    case STUCK = 'stuck';
}
