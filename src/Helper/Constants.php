<?php

namespace App\Helper;

class Constants
{

    //region Admin Controller
    const ADMIN_USER_LIST_URL = '/admin/users';
    const ADMIN_USER_LIST_NAME = 'admin_user_list';
    const ADMIN_USER_LIST_VIEW = 'admin/list_user.html.twig';

    const ADMIN_USER_CREATE_URL = '/admin/users/create';
    const ADMIN_USER_CREATE_NAME = 'admin_user_create';
    const ADMIN_USER_CREATE_VIEW = 'admin/create_user.html.twig';

    const ADMIN_USER_EDIT_URL = '/admin/users/{id}/edit';
    const ADMIN_USER_EDIT_NAME = 'admin_user_edit';
    const ADMIN_USER_EDIT_VIEW = 'admin/edit_user.html.twig';
    //endregion

    //region User Controller
    const USER_GENERATED_PASSWORD_URL = '/users/{id}/edit/generated_password';
    const USER_GENERATED_PASSWORD_NAME = 'user_edit_generated_password';
    const USER_GENERATED_PASSWORD_VIEW = 'user/edit.password.html.twig';

    const USER_EDIT_URL = '/users/{id}/edit';
    const USER_EDIT_NAME = 'user_edit';
    const USER_EDIT_VIEW = 'user/edit.html.twig';
    //endregion


    //region Task Controller
    const TASK_LIST_URL = '/tasks/{status}/{page}';
    const TASK_LIST_NAME = 'task_list';
    const TASK_LIST_VIEW = 'task/list.html.twig';

    const TASK_CREATE_URL = '/tasks/create';
    const TASK_CREATE_NAME = 'task_create';
    const TASK_CREATE_VIEW = 'task/create.html.twig';

    const TASK_EDIT_URL = '/tasks/{id}/edit';
    const TASK_EDIT_NAME = 'task_edit';
    const TASK_EDIT_VIEW = 'task/edit.html.twig';

    const TASK_DELETE_URL = '/tasks/{id}/delete';
    const TASK_DELETE_NAME = 'task_delete';

    const TASK_TOGGLE_URL = '/tasks/{id}/toggle';
    const TASK_TOGGLE_NAME = 'task_toggle';
    //endregion




}
