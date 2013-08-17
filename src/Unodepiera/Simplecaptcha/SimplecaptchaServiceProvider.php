<?php namespace Unodepiera\Simplecaptcha;

use Illuminate\Support\ServiceProvider;

class SimplecaptchaServiceProvider extends ServiceProvider {

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
		$this->package('unodepiera/simplecaptcha');
		require __DIR__ . '/validation.php';
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app["simplecaptcha"] = $this->app->share(function($app){
			return new Simplecaptcha;
		});
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