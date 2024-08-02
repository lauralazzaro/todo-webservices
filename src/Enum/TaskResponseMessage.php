<?php

namespace App\Enum;

enum TaskResponseMessage: string
{
    case TASK_SAVED = 'The task has been saved.';
    case TASK_MODIFIED = 'The task has been modified.';
    case TASK_DELETED = 'The task has been successfully deleted.';
    case TASK_STATUS_CHANGED = 'The task status has been successfully modified.';
    case TASK_STATUS_MODIFIED = 'The status of the task has been modified.';
    case INVALID_FORM_SUBMISSION = 'Invalid form submission.';
    case INTERNAL_SERVER_ERROR = 'Internal server error.';
}
