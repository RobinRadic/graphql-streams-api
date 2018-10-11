<?php namespace Radic\GraphqlStreamsApiModule;

use Anomaly\Streams\Platform\Addon\AddonServiceProvider;
use Illuminate\Routing\Router;

class GraphqlStreamsApiModuleServiceProvider extends AddonServiceProvider
{
    protected $commands = [
        Console\GenerateApiCommand::class,
        \Nuwave\Lighthouse\Console\ClearCacheCommand::class,
        \Nuwave\Lighthouse\Console\ValidateSchemaCommand::class,
        \Nuwave\Lighthouse\Console\PrintSchemaCommand::class,
    ];

    protected $routes = [
        'admin/graphql_streams_api' => 'Radic\GraphqlStreamsApiModule\Http\Controller\Admin\GraphQLStreamsApiController@index',
        'admin/graphql_streams_api/default_field_resolutions' => 'Radic\GraphqlStreamsApiModule\Http\Controller\Admin\GraphQLStreamsApiController@default_field_resolutions'
    ];

    protected $providers = [
        GraphQL\GraphQLServiceProvider::class
    ];

    /**
     * Register the addon.
     */
    public function register()
    {
        $console = $this->app->make(\Illuminate\Contracts\Console\Kernel::class);
        foreach ($this->commands as $command) {
            $console->registerCommand($this->app->make($command));
        }
    }

    /**
     * Boot the addon.
     */
    public function boot()
    {
        // Run extra post-boot registration logic here.
        // Use method injection or commands to bring in services.
    }

    /**
     * Map additional addon routes.
     *
     * @param Router $router
     */
    public function map(Router $router)
    {
        // Register dynamic routes here for example.
        // Use method injection or commands to bring in services.
    }

}
