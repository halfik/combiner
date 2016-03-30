<?php namespace Netinteractive\Combiner;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

class CombinerServiceProvider extends ServiceProvider
{

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
        $config     = realpath(__DIR__.'/../../config/config.php');
        $this->mergeConfigFrom($config, 'netinteractive.combiner');


        $this->publishes([
            __DIR__.'/../../config/config.php' => config_path('/packages/netinteractive/combiner/config.php'),
        ], 'config');
    }

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        $this->commands($this->commands);
        $this->app->bind('combiner', function () {
            return new Combiner();
        });

        $this->app->booting(function()
        {
            AliasLoader::getInstance()->alias('Combiner','Netinteractive\Combiner\Facades\CombinerFacade');
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
