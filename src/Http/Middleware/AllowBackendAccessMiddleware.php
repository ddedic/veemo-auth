<?php namespace App\Modules\Users\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Config\Repository;
use Illuminate\Http\RedirectResponse;

class AllowBackendAccessMiddleware {

	/**
	 * The Guard implementation.
	 *
	 * @var Guard
	 */
	protected $auth;


    protected $config;

	/**
	 * Create a new filter instance.
	 *
	 * @param  Guard  $auth
     * @param  Repository $config
	 */
	public function __construct(Guard $auth, Repository $config)
	{
		$this->auth = $auth;
        $this->config = $config;
	}

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		if (!$this->auth->check())
		{
            if ($this->config->get('veemo.core.allowBackendAccessBeforeAuth') == false)
            {
                return new RedirectResponse(route('frontend.homepage'));
            }
		}

		return $next($request);
	}

}
