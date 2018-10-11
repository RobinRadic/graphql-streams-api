<?php

namespace Radic\GraphqlStreamsApiModule\GraphQL;

use GraphQL\Validator\Rules\DisableIntrospection;
use Illuminate\Support\ServiceProvider;

class GraphQLServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->overrideVendorConfig();
        $this->app->singleton(GraphApi::class);
        $this->app->alias(GraphApi::class, 'graphql');
        $this->app->register(\Nuwave\Lighthouse\Providers\LighthouseServiceProvider::class);
        $this->app->alias(GraphApi::class, 'graphql');
    }

    public function boot()
    {
        $this->registerDirectives();
    }

    protected function registerDirectives()
    {
        $classes  = config('radic.module.graphql_streams_api::config.schema.directives', []);
        $registry = graphql()->directives();
        foreach ($classes as $directiveClass) {
            $registry->register(resolve($directiveClass));
        }
    }

    protected function overrideVendorConfig()
    {
        $this->app[ 'config' ]->set('lighthouse', [
            'route'      => config('radic.module.graphql_streams_api::config.endpoint'),
            'schema'     => [
                'register' => config('radic.module.graphql_streams_api::config.schema.output'), // __DIR__ . '/../../resources/schema/schema.graphqls',
            ],
            'namespaces' => [
                'models'    => 'Radic\\GraphqlStreamsApiModule\\GraphQL\\Models',
                'mutations' => 'Radic\\GraphqlStreamsApiModule\\GraphQL\\Mutations',
                'queries'   => 'Radic\\GraphqlStreamsApiModule\\GraphQL\\Queries',
                'scalars'   => 'Radic\\GraphqlStreamsApiModule\\GraphQL\\Scalars',
            ],
            'controller' => \Radic\GraphqlStreamsApiModule\Http\Controller\ApiGraphQLController::class . '@query',
//        'route_name'       => 'graphql',
//        'route_enable_get' => true,
//            'security'   => [
//                'max_query_complexity'  => 10,
//                'max_query_depth'       => 10,
//                'disable_introspection' => DisableIntrospection::DISABLED,
//            ],
//            'controller' => 'Nuwave\Lighthouse\Support\Http\Controllers\GraphQLController@query',
//        'cache'            => [
//            'enable' => env('LIGHTHOUSE_CACHE_ENABLE', false),
//            'key'    => env('LIGHTHOUSE_CACHE_KEY', 'lighthouse-schema'),
//        ],
//        'directives' => [ __DIR__ . '/../src/Api/Directives' ],
        ]);
    }


}