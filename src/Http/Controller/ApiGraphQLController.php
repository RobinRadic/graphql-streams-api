<?php

namespace Radic\GraphqlStreamsApiModule\Http\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Nuwave\Lighthouse\Support\Http\Controllers\GraphQLController;
use Radic\GraphqlStreamsApiModule\GraphQL\Schema\Context;

class ApiGraphQLController extends GraphQLController
{

    /**
     * Execute GraphQL query.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function query(Request $request)
    {
        $query     = $request->input('query');
        $variables = $request->input('variables');

        if (\is_string($variables)) {
            $variables = json_decode($variables, true);
        }

        $user    = app()->bound('auth') ? auth()->user() : null;
        $context = new Context($request, $user);

        $result  = graphql()->execute(
            $query,
            $context,
            $variables
        );

        return response($result);
    }
}