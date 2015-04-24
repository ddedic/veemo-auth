<?php namespace Veemo\Auth\Http\Middleware;

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Session\Store;
use Illuminate\Contracts\Auth\Guard;

class AuthFrontendMiddleware
{
    /**
     * @var Authentication
     */
    private $auth;
    /**
     * @var SessionManager
     */
    private $session;
    /**
     * @var Request
     */
    private $request;
    /**
     * @var Redirector
     */
    private $redirect;
    /**
     * @var Application
     */
    private $application;

    public function __construct(Guard $auth, Store $session, Request $request, Redirector $redirect, Application $application)
    {
        $this->auth = $auth;
        $this->session = $session;
        $this->request = $request;
        $this->redirect = $redirect;
        $this->application = $application;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        // Check if the user is logged in
        if (!$this->auth->check()) {
            // Store the current uri in the session
            $this->session->put('url.intended', $this->request->url());

            // Redirect to the login page
            return $this->redirect->route('frontend.login');
        }


		$permissions = $this->getPermissions($request);
		
		if (!is_null($permissions)) {
    		if ($this->auth->check() && !$request->user()->can($permissions))
    		{
		        flash()->error('Access denied. Insufficient permissions.');
		        return redirect()->route('frontend.homepage');
    		}
		}



        return $next($request);
    }
    
    
    

	/**
	 * Get the required permsissions.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array
	 **/
	private function getPermissions($request)
	{
	    $action = $request->route()->getAction();
	 
	    return isset($action['permissions']) ? $action['permissions'] : null;
	}   
    
        
    
    
}