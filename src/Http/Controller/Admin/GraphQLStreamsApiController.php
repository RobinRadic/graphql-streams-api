<?php

namespace Radic\GraphqlStreamsApiModule\Http\Controller\Admin;

use Anomaly\Streams\Platform\Http\Controller\AdminController;

class GraphQLStreamsApiController extends AdminController
{
    public function index()
    {
        /** @var \Radic\GraphqlStreamsApiModule\Generator\Generator $generator */
        $generator = graphql()->getGenerator();

        return $this->view->make('module::admin.overview', []);
    }
}