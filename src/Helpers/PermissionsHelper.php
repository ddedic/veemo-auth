<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Debug\Dumper;

/**
 * License: MIT
 * Copyright (c) 2015 Charl Gottschalk
 * Github: https://github.com/Cloud5Ideas
 * @package cloud5ideas/appkit
 */
if ( ! function_exists('admin_url')) {

    /**
     * Generate the admin url.
     *
     * @return string
     */
    function admin_url($url = null)
    {
        return;
    }
}

/**
 * License: MIT
 * Copyright (c) 2015 Charl Gottschalk
 */
if ( ! function_exists('user_can')) {

    /**
     * Check if a user has permission.
     *
     * @param  string $permission
     * @param  bool|string|null $flash
     * @param  \App\Modules\Users\Models\User|null $user
     * @param  mixed $func
     * @param  mixed $params
     * @return bool
     */
    function user_can($permission, $flash = null, $user = null, $func = null, $params = null)
    {
        $can = false;

        if (!is_null($func) && $func instanceof Closure && !is_null($user)) {
            $can =  $func($params);
        }

        if (!$can) {
            if (!is_null($user)) {
                $can = $user->can($permission);
            } else {
                $can = Auth::user()->can($permission);
            }
        }

        if (!$can && !is_null($flash)) {
            // Flash a message to the session if set.
            if (is_bool($flash)) {
                flash()->error('Access denied. Insufficient permission.');
            } else {
                flash()->error($flash);
            }
        }

        return $can;
    }
}

/**
 * License: MIT
 * Copyright (c) 2015 Charl Gottschalk
 */
if ( ! function_exists('user_is')) {

    /**
     * Check if a user has permission.
     *
     * @param  string $role
     * @param  bool|string|null $flash
     * @param  \App\Modules\Users\Models\User|$user
     * @param  mixed $func
     * @return bool
     */
    function user_is($role, $flash = null, $user = null, $func = null)
    {
        $is = false;

        if (!is_null($func) && $func instanceof Closure && !is_null($user)) {
            $is =  $func($user);
        }

        if (!$is) {
            if (!is_null($user)) {
                $is = $user->is($permission);
            } else {
                $is = Auth::user()->is($permission);
            }
        }

        if (!$is && !is_null($flash)) {
            // Flash a message to the session if set.
            if (is_bool($flash)) {
                flash()->error(config('Access denied. Insufficient permission.'));
            } else {
                flash()->error($flash);
            }
        }

        return $is;
    }
}

/**
 * License: MIT
 * Github: https://github.com/laracasts
 * @package laracasts/flash
 */
if ( ! function_exists('flash')) {

    /**
     * Arrange for a flash message.
     *
     * @param  string|null $message
     * @return \Laracasts\Flash\FlashNotifier
     */
    function flash($message = null)
    {
        $notifier = app('flash');

        if ( ! is_null($message)) {
            return $notifier->info($message);
        }

        return $notifier;
    }

}