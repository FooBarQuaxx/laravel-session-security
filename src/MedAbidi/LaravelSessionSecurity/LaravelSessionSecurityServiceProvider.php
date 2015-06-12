<?php namespace MedAbidi\LaravelSessionSecurity;

use Illuminate\Support\ServiceProvider;

use MedAbidi\LaravelSessionSecurity\Middleware\SessionSecurity;

class LaravelSessionSecurityServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('med-abidi/laravel-session-security');

        include __DIR__.'/../../routes.php';
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        $this->registerSessionSecureMiddleware();
	}

    protected function registerSessionSecureMiddleware()
    {
        $this->app->middleware(SessionSecurity::class);
    }
	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
