<?php namespace Radic\GraphqlStreamsApiModule;

use Anomaly\Streams\Platform\Addon\AddonServiceProvider;
use Illuminate\Routing\Router;

class GraphqlStreamsApiModuleServiceProvider extends AddonServiceProvider
{
    protected $commands = [
        Console\GenerateApiCommand::class
    ];

    protected $routes = [
        'admin/graphql_streams_api' => 'Radic\GraphqlStreamsApiModule\Http\Controller\Admin\GraphQLStreamsApiController@index'
    ];

    protected $providers = [
        GraphQL\GraphQLServiceProvider::class
    ];

    /**
     * Register the addon.
     */
    public function register()
    {
        // Run extra pre-boot registration logic here.
        // Use method injection or commands to bring in services.
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
