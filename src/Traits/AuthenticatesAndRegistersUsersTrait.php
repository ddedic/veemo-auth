<?php namespace Veemo\Auth\Traits;

use App\Modules\Users\Models\Role;
use App\Modules\Users\Services\Registrar;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Session;


trait AuthenticatesAndRegistersUsersTrait
{

    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * The registrar implementation.
     *
     * @var Registrar
     */
    protected $registrar;

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getRegister()
    {
        return $this->theme->view('modules.users.auth.register')->render();
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function postRegister(Request $request)
    {
        $validator = $this->registrar->validator($request->all());

        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }

        $user = $this->registrar->create($request->all());
        $role = Role::where('slug', '=', config('veemo.core.users_default_user_role'))->firstOrFail();
        $user->syncRoles([$role->id]);
        $this->auth->login($user);

        return redirect($this->redirectRoute());
    }

    /**
     * Get the post register / login redirect path.
     *
     * @return string
     */
    public function redirectRoute()
    {
        return property_exists($this, 'redirectRoute') ? $this->redirectRoute : 'frontend.homepage';
    }


    /**
     * Get the logout redirect path.
     *
     * @return string
     */
    public function redirectLogoutRoute()
    {
        return property_exists($this, 'redirectLogoutRoute') ? $this->redirectLogoutRoute : 'frontend.homepage';
    }

    /**
     * Show the application login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogin()
    {
        return $this->theme->view('modules.users.auth.login')->render();
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function postLogin(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email', 'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if ($this->auth->attempt($credentials, $request->has('remember'))) {

            $intended = Session::pull('url.intended');

            if (is_null($intended))
            {
                return redirect()->route($this->redirectRoute());
            }

            return redirect()->intended($intended);
        }

        return redirect()->route($this->loginRoute())
            ->withInput($request->only('email', 'remember'))
            ->withErrors([
                'email' => 'These credentials do not match our records.',
            ]);
    }

    /**
     * Get the path to the login route.
     *
     * @return string
     */
    public function loginRoute()
    {
        return property_exists($this, 'loginRoute') ? $this->loginRoute : 'frontend.login';
    }

    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogout()
    {
        $this->auth->logout();

        return redirect()->route($this->redirectLogoutRoute());
    }

}