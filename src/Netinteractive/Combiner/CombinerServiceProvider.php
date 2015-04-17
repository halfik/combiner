<?php namespace Netinteractive\Combiner;

use Illuminate\Support\ServiceProvider;

class CombinerServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

    protected $commands = [
        'Netinteractive\Combiner\Commands\Clean',
    ];

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{

	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        $this->commands($this->commands);
        $this->app->bind('Combiner','Netinteractive\Combiner\Combiner');
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
