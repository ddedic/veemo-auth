<?php namespace Veemo\Auth\Traits;

use Illuminate\Database\Eloquent\Collection;

/**
 * License: MIT
 * Copyright (c) 2015 Shea Lewis
 * Github: https://github.com/caffeinated
 * @package caffeinated/shinobi
 */
trait PermissionsTrait
{
    /*
    |----------------------------------------------------------------------
    | Role Trait Methods
    |----------------------------------------------------------------------
    |
    */

    /**
     * Users can have many roles.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function roles()
    {
        return $this->belongsToMany('\App\Modules\Users\Models\Role')->withTimestamps();
    }

    /**
     * Get all user roles.
     *
     * @return array|null
     */
    public function getRoles()
    {
        if (! is_null($this->roles)) {
            return $this->roles->lists('slug');
        }

        return null;
    }

    /**
     * Checks if the user a superuser.
     *
     * @return bool
     */
    public function isSuperuser()
    {

        return $this->is_superuser;
    }

    /**
     * Checks if the user is activated.
     *
     * @return bool
     */
    public function isActive()
    {

        return $this->status == 'active' ? true : false;
    }

    /**
     * Checks if the user is inactive.
     *
     * @return bool
     */
    public function isInactive()
    {

        return $this->status == 'inactive' ? true : false;
    }

    /**
     * Checks if the user is banned.
     *
     * @return bool
     */
    public function isBanned()
    {

        return $this->status == 'banned' ? true : false;
    }

    /**
     * Disable user
     *
     **/
    public function deactivate() {
        $this->status = 'inactive';
        $this->save();
    }

    /**
     * Enable user
     *
     **/
    public function activate() {
        $this->status = 'active';
        $this->save();
    }


    /**
     * Ban user
     *
     **/
    public function ban() {
        $this->status = 'banned';
        $this->save();
    }

    /**
     * Checks if the user has the given role.
     *
     * @param  string $slug
     * @return bool
     */
    public function is($slug)
    {
        if ( $this->is_superuser ) return true;

        $slug = strtolower($slug);

        foreach ($this->roles as $role) {
            if ($role->slug == $slug) return true;
        }

        return false;
    }

    /**
     * Assigns the given role to the user.
     *
     * @param  int $roleId
     * @return bool
     */
    public function assignRole($roleId = null)
    {
        $roles = $this->roles;

        if (! $roles->contains($roleId)) {
            return $this->roles()->attach($roleId);
        }

        return false;
    }

    /**
     * Revokes the given role from the user.
     *
     * @param  int $roleId
     * @return bool
     */
    public function revokeRole($roleId = '')
    {
        return $this->roles()->detach($roleId);
    }

    /**
     * Syncs the given role(s) with the user.
     *
     * @param  array $roleIds
     * @return bool
     */
    public function syncRoles(array $roleIds)
    {
        return $this->roles()->sync($roleIds);
    }

    /**
     * Revokes all roles from the user.
     *
     * @return bool
     */
    public function revokeAllRoles()
    {
        return $this->roles()->detach();
    }

    /*
    |----------------------------------------------------------------------
    | Permission Trait Methods
    |----------------------------------------------------------------------
    |
    */

    /**
     * Get all user role permissions.
     *
     * @return array|null
     */
    public function getPermissions()
    {
        foreach ($this->roles as $role) {
            $permissions[] = $role->getPermissions();
        }

        $permissions = call_user_func_array('array_merge', $permissions);

        return $permissions;
    }

    /**
     * Check if user has the given permission.
     *
     * @param  string $permission
     * @return bool
     */
    public function can($permission)
    {
        if ( $this->is_superuser ) return true;

        $can = false;

        foreach ($this->roles as $role){
            if ($role->can($permission)) {
                $can = true;
            }
        }

        return $can;
    }

    /*
    |----------------------------------------------------------------------
    | Magic Methods
    |----------------------------------------------------------------------
    |
    */

    /**
     * Magic __call method to handle dynamic methods.
     *
     * @param  string $method
     * @param  array  $arguments
     * @return mixed
     */
    public function __call($method, $arguments = array())
    {
        // Handle isRoleslug() methods
        if (starts_with($method, 'is') and $method !== 'is') {
            $role = substr($method, 2);

            return $this->is($role);
        }

        // Handle canDoSomething() methods
        if (starts_with($method, 'can') and $method !== 'can') {
            $permission = substr($method, 3);

            return $this->can($permission);
        }

        return parent::__call($method, $arguments);
    }

    /**
     * Get inactive users
     *
     * @param \Illuminate\Database\Eloquent $query
     **/
    public function scopeInactive($query) {
        $query->where('status' ,'=', 'inactive');
    }

    /**
     * Get active users
     *
     * @param \Illuminate\Database\Eloquent $query
     **/
    public function scopeActive($query) {
        $query->where('status' ,'=', 'active');
    }

    /**
     * Get banned users
     *
     * @param \Illuminate\Database\Eloquent $query
     **/
    public function scopeBanned($query) {
        $query->where('status' ,'=', 'banned');
    }

    /**
     * Get superusers
     *
     * @param \Illuminate\Database\Eloquent $query
     **/
    public function scopeIsSuperUser($query) {
        $query->where('is_superuser' ,'=', true);
    }

    /**
     * Get non superusers
     *
     * @param \Illuminate\Database\Eloquent $query
     **/
    public function scopeIsNotSuperUser($query) {
        $query->where('is_superuser' ,'=', false);
    }
}