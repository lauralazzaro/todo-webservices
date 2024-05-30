<?php

namespace App\Helper;

class Constants
{
    //region Admin Controller
    public const ADMIN_USER_LIST_URL = '/admin/users';
    public const ADMIN_USER_LIST_NAME = 'admin_user_list';
    public const ADMIN_USER_LIST_VIEW = 'admin/list_user.html.twig';

    public const ADMIN_USER_CREATE_URL = '/admin/users/create';
    public const ADMIN_USER_CREATE_NAME = 'admin_user_create';
    public const ADMIN_USER_CREATE_VIEW = 'admin/create_user.html.twig';

    public const ADMIN_USER_EDIT_URL = '/admin/users/{id}/edit';
    public const ADMIN_USER_EDIT_NAME = 'admin_user_edit';
    public const ADMIN_USER_EDIT_VIEW = 'admin/edit_user.html.twig';
    //endregion

    //region User Controller
    public const USER_GENERATED_PASSWORD_URL = '/users/{id}/edit/generated_password';
    public const USER_GENERATED_PASSWORD_NAME = 'user_edit_generated_password';
    public const USER_GENERATED_PASSWORD_VIEW = 'user/edit.password.html.twig';

    public const USER_EDIT_URL = '/users/{id}/edit';
    public const USER_EDIT_NAME = 'user_edit';
    public const USER_EDIT_VIEW = 'user/edit.html.twig';
    //endregion


    //region Task Controller
    public const TASK_LIST_URL = '/tasks/{status}/{page}';
    public const TASK_LIST_NAME = 'task_list';
    public const TASK_LIST_VIEW = 'task/list.html.twig';

    public const TASK_CREATE_URL = '/tasks/create';
    public const TASK_CREATE_NAME = 'task_create';
    public const TASK_CREATE_VIEW = 'task/create.html.twig';

    public const TASK_EDIT_URL = '/tasks/{id}/edit';
    public const TASK_EDIT_NAME = 'task_edit';
    public const TASK_EDIT_VIEW = 'task/edit.html.twig';

    public const TASK_DELETE_URL = '/tasks/{id}/delete';
    public const TASK_DELETE_NAME = 'task_delete';

    public const TASK_TOGGLE_URL = '/tasks/{id}/toggle';
    public const TASK_TOGGLE_NAME = 'task_toggle';
    //endregion

    //region Authentication
    public const LOGIN = '/login';
    public const LOGOUT = '/logout';
    //endregion

    //region Task
    public const TASK_STATUS_TODO = 'todo';
    public const TASK_STATUS_DONE = 'done';
    //endregion
}
