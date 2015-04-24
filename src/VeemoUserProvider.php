<?php namespace Veemo\Auth;

use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Auth\EloquentUserProvider;

class VeemoUserProvider extends  EloquentUserProvider {


    /**
     * Create a new database user provider.
     *
     * @param  \Illuminate\Contracts\Hashing\Hasher  $hasher
     * @param  string  $model
     * @return void
     */
    public function __construct(HasherContract $hasher, $model)
    {
        parent::__construct($hasher, $model);

        //
    }


    /**
     * Get user status. Check is user active, inactive or banned.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return bool
     */
    public function getStatus(UserContract $user)
    {
        return $user->status;
    }


    /**
     * Retrieve a user by their activation token.
     *
     * @param  string  $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByActivationToken($token)
    {
        $model = $this->createModel();

        return $model->newQuery()
            ->where($model->getActivationTokenName(), $token)
            ->first();
    }


    /**
     * Retrieve a user by their activation token.
     *
     * @param  string  $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function generateActivationToken()
    {
        $model = $this->createModel();

        return $model->setActivationToken();
    }

}
