<?php

namespace Veemo\Auth;

use Illuminate\Contracts\Auth\UserProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Illuminate\Auth\Guard;


class VeemoGuard extends Guard {


    /**
     * User status: activated
     */
    const USER_ACTIVE = 'active';

    /**
     * User status: not activated
     */
    const USER_INACTIVE = 'inactive';

    /**
     * User status: banned
     */
    const USER_BANNED = 'banned';




    /**
     * @param UserProvider $provider
     * @param SessionInterface $session
     * @param Request $request
     */
    public function __construct(UserProvider $provider, SessionInterface $session, Request $request = null)
    {
        parent::__construct($provider, $session, $request);

    }


    /**
     * Attempt to authenticate a user using the given credentials.
     *
     * @param  array  $credentials
     * @param  bool   $remember
     * @param  bool   $login
     * @return bool
     */
    public function attempt(array $credentials = [], $remember = false, $login = true)
    {
        $this->fireAttemptEvent($credentials, $remember, $login);

        $this->lastAttempted = $user = $this->provider->retrieveByCredentials($credentials);

        // If an implementation of UserInterface was returned, we'll ask the provider
        // to validate the user against the given credentials, and if they are in
        // fact valid we'll log the users into the application and return true.
        if ($this->hasValidCredentials($user, $credentials))
        {
            if($this->hasValidStatus($user))
            {
                if ($login) $this->login($user, $remember);

                return true;
            }

        }

        return false;
    }



    /**
     * Determine if the user has valid status. Needs to be activated, otherwise throw exception.
     *
     * @param  mixed  $user
     * @return bool
     */
    protected function hasValidStatus($user)
    {
        $status = ! is_null($user) ? $this->provider->getStatus($user) : null;


        switch ($status)
        {

            case self::USER_INACTIVE:
                throw new Exceptions\UserNotActivatedException('User not active.');
            break;

            case self::USER_BANNED:
                throw new Exceptions\UserBannedException('User is banned.');
            break;

            case self::USER_ACTIVE:
                return true;
            break;
        }

    }



} 