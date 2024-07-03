<?php

namespace App\Enum;

enum TaskStatus: string
{
    case TODO = 'todo';
    case IN_PROGRESS = 'in-progress';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case OVERDUE = 'overdue';
    case ON_HOLD = 'on-hold';
}
