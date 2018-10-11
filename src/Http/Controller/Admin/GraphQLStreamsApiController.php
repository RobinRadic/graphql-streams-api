<?php

namespace Radic\GraphqlStreamsApiModule\Http\Controller\Admin;

use Anomaly\Streams\Platform\Http\Controller\AdminController;

class GraphQLStreamsApiController extends AdminController
{
    public function index()
    {
        return $this->view->make('pyradic.module.core::admin.api', []);
    }
}