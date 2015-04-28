<?php namespace Veemo\Auth\Traits;

use App\Modules\Users\Models\Role;
use App\Modules\Users\Services\Registrar;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Session;
use Veemo\Auth\Exceptions\UserBannedException;
use Veemo\Auth\Exceptions\UserNotActivatedException;

use App\Modules\Users\Events\UserHasRegistered;
use App\Modules\Users\Events\UserHasBegunActivationProcess;


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
     * The event dispatcher instance.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $events;


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
        $role = Role::where('slug', '=', config('veemo.auth.users_default_user_role'))->firstOrFail();
        $user->syncRoles([$role->id]);

        $needActivation = config('veemo.auth.forceActivationRegistration');
        $showPasswordInEmail = config('veemo.auth.showPasswordInWelcomeEmail');
        $rawPassword = $request->input('password');


        if ($needActivation) {

            $emailView = $this->theme->locate('modules.users.email.activation');

            // Fire UserHasBegunActivationProcess Event
            $this->events->fire(new UserHasBegunActivationProcess($user, $emailView));

            flash()->info('Succesfully registered. Please check your email, and click activation link to activate your account.');
            return redirect()->route('frontend.login');

        } else {

            $emailView = $this->theme->locate('modules.users.email.welcome');

            // Fire UserHasBegunActivationProcess Event
            $this->events->fire(new UserHasRegistered($user, $showPasswordInEmail, $rawPassword, $emailView));

            $user->activate();

            $this->auth->login($user);

            return redirect()->route($this->redirectRoute());

        }

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


        try {

            if ($this->auth->attempt($credentials, $request->has('remember'))) {

                $intended = Session::pull('url.intended');

                if (is_null($intended)) {
                    return redirect()->route($this->redirectRoute());
                }

                return redirect()->intended($intended);

            } else {
                $errors = ['email' => 'These credentials do not match our records.'];
            }

        } catch (UserNotActivatedException $e) {
            $errors = ['error' => 'You account is not activated.'];

        } catch (UserBannedException $e) {
            $errors = ['error' => 'You account is banned.'];

        } catch (\Exception $e) {
            \Log::error('Internal error. postLogin method. Error message: ' . $e->getMessage());
            $errors = ['error' => 'Internal server error.'];
        }


        return redirect()->route($this->loginRoute())
            ->withInput($request->only('email', 'remember'))
            ->withErrors($errors);
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

    /**
     * Get the logout redirect path.
     *
     * @return string
     */
    public function redirectLogoutRoute()
    {
        return property_exists($this, 'redirectLogoutRoute') ? $this->redirectLogoutRoute : 'frontend.homepage';
    }

}