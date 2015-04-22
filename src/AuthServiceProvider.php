<?php namespace Veemo\Core;

use App, Config, Lang, View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Routing\Router;



class CoreServiceProvider extends ServiceProvider
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
        'backend.force.ssl'       => 'BackendForceSslMiddleware',
        'frontend.force.ssl'      => 'FrontendForceSslMiddleware'
    ];



    public function boot()
    {

        // Publish config.
        $this->publishes([
            __DIR__ . '/Publish/Config/core.php' => config_path('veemo/core.php'),
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
            __DIR__ . '/Publish/Config/core.php', 'veemo.core'
        );

        $this->registerServices();
        
        $this->registerAliases();

        $this->registerNamespaces();

        $this->registerModules();

        $this->registerThemes();

        $this->registerConsoleCommands();

        $this->registerHelpers();

        $this->registerDefaultRoutes();

        $this->registerMiddlewares($this->app->router);
    }


    protected function registerServices()
    {
        // HTML + FORM
        $this->app->register('Illuminate\Html\HtmlServiceProvider');
      
        
    }


    protected function registerAliases()
    {
		$aliases = AliasLoader::getInstance();
		
		// FORM
		$aliases->alias(
            'Form',
            'Illuminate\Html\FormFacade'
        );
        
        // HTML
        $aliases->alias(
            'Html',
            'Illuminate\Html\HtmlFacade'
        );        
    }


    protected function registerModules()
    {
        $this->app->register('Veemo\Modules\ModulesServiceProvider');
        AliasLoader::getInstance()->alias('Module', 'Veemo\Modules\Facades\Module');
    }


    protected function registerThemes()
    {
        $this->app->register('Veemo\Themes\ThemeServiceProvider');
        AliasLoader::getInstance()->alias('Theme', 'Veemo\Themes\Facades\Theme');
    }



    protected function registerHelpers()
    {
        foreach (glob(__DIR__ . '/Helpers/*Helper.php') as $filename){
            require_once($filename);
        }
    }


    public function registerDefaultRoutes()
    {
        require (__DIR__ . '/Http/routes.php');
    }



    public function registerMiddlewares(Router $router)
    {
        foreach ($this->middlewares as $name => $middleware) {
            $class = "Veemo\\Core\\Http\\Middleware\\{$middleware}";
            $router->middleware($name, $class);
        }
    }


    /**
     * Register the Core module resource namespaces.
     *
     * @return void
     */
    protected function registerNamespaces()
    {

        //

    }


    /**
     * Register the package console commands.
     *
     * @return void
     */
    protected function registerConsoleCommands()
    {
        $this->registerInstallCommand();

        $this->commands([
            'veemo.install'
        ]);
    }


    /**
     * Register the "veemo:install" console command.
     *
     * @return Console\InstallCommand
     */
    protected function registerInstallCommand()
    {
        $this->app->bindShared('veemo.install', function ($app) {
            $handler = new Handlers\InstallHandler();
            return new Console\InstallCommand($handler);
        });
    }


}
