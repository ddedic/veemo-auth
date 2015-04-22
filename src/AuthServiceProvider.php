<?php namespace Veemo\Auth;

use App, Config, Lang, View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;



class AuthServiceProvider extends ServiceProvider
{

    /**
     * @var bool $defer Indicates if loading of the provider is deferred.
     */
    protected $defer = false;


    /**
     * The filters base class name.
     *
     * @var array
     */
    protected $middlewares = [
        'allow.backend.access'      => 'AllowBackendAccessMiddleware',
        'auth.frontend'             => 'AuthFrontendMiddleware',
        'guest.frontend'            => 'GuestFrontendMiddleware',
        'auth.backend'              => 'AuthBackendMiddleware',
        'guest.backend'             => 'GuestBackendMiddleware'
    ];



    public function boot()
    {

        // Publish config.
        $this->publishes([
            __DIR__ . '/Config/auth.php' => config_path('veemo/auth.php'),
        ]);


    }



    /**
     * Register the Core module service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/Config/auth.php', 'veemo.auth'
        );


        $this->registerHelpers();

        $this->registerMiddlewares($this->app->router);
    }






    protected function registerHelpers()
    {
        foreach (glob(__DIR__ . '/Helpers/*Helper.php') as $filename){
            require_once($filename);
        }
    }



    public function registerMiddlewares(Router $router)
    {
        foreach ($this->middlewares as $name => $middleware) {
            $class = "Veemo\\Auth\\Http\\Middleware\\{$middleware}";
            $router->middleware($name, $class);
        }
    }




}
