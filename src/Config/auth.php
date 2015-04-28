<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Frontend auth prefix
    |--------------------------------------------------------------------------
    |
    |
    | Set frontend auth prefix for login, register, password reset etc.
    |
    | Default: users
    |
    */

    'frontendAuthPrefix' => null,



    /*
    |--------------------------------------------------------------------------
    | Backend auth prefix
    |--------------------------------------------------------------------------
    |
    |
    | Set frontend auth prefix for login, register, password reset etc.
    |
    | Default: null
    |
    */

    'backendAuthPrefix' => null,



    /*
    |--------------------------------------------------------------------------
    | Force Activation after Registration
    |--------------------------------------------------------------------------
    |
    |
    | Force user activation after registration (frontend)
    |
    | Default: null
    |
    */

    'forceActivationRegistration' => true,


    /*
    |--------------------------------------------------------------------------
    | Show password in welcome email
    |--------------------------------------------------------------------------
    |
    |
    | Do we show raw password in welcome email?
    |
    | Default: false
    |
    */
    'showPasswordInWelcomeEmail' => true,



    /*
    |--------------------------------------------------------------------------
    | Default Admin use role
    |--------------------------------------------------------------------------
    |
    |
    | The default role to assigned to admin user.
    |
    | Default: app.user
    |
    */

    'users_default_admin_role' => 'app.admin',


    /*
    |--------------------------------------------------------------------------
    | Default Registration Role
    |--------------------------------------------------------------------------
    |
    |
    | The default role to assign to registered users.
    |
    | Default: app.user
    |
    */

    'users_default_user_role' => 'app.user',

);