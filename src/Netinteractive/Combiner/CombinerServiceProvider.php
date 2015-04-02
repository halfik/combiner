<?php namespace Netinteractive\Combiner;

use Illuminate\Support\ServiceProvider;

class CombinerServiceProvider extends ServiceProvider {

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
		$this->package('netinteractive/combiner');

        $this->app['combiner:clean'] = $this->app->share(function($app)
        {
            return new Commands\Clean;
        });
        $this->commands('combiner:clean');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//
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
