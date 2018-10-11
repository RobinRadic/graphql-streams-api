<?php

namespace Radic\GraphqlStreamsApiModule\Http\Controller\Admin;

use Anomaly\Streams\Platform\Http\Controller\AdminController;

class GraphQLStreamsApiController extends AdminController
{
    public function index()
    {
        /** @var \Radic\GraphqlStreamsApiModule\Generator\Generator $generator */
        $generator = graphql()->getGenerator();

        $schemeBindings = collect($generator->getSchemeBindingNames())->map(function ($name) use ($generator) {
            list($namespace, $slug) = explode('::', $name);
            return array_merge(compact('name', 'namespace', 'slug'), $generator->getSchemeBinding($name)->toArray());
        })->keyBy('name')->toArray();

        return $this->view->make('module::admin.overview', [
            'scheme_bindings'      => $schemeBindings,
            'scheme_binding_names' => $generator->getSchemeBindingNames(),
            'graphql_config'       => config('radic.module.graphql_streams_api::config'),
        ]);
    }

    public function default_field_resolutions()
    {
        return $this->view->make('module::admin.default_field_resolutions', [
            'default_field_resolutions' => config('radic.module.graphql_streams_api::config.default_field_resolutions'),
        ]);
    }
}