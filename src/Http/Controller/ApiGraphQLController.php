<?php

namespace Radic\GraphqlStreamsApiModule\Http\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Nuwave\Lighthouse\GraphQL;
use Nuwave\Lighthouse\Schema\Extensions\ExtensionRegistry;
use Nuwave\Lighthouse\Schema\Extensions\ExtensionRequest;
use Nuwave\Lighthouse\Schema\MiddlewareRegistry;
use Nuwave\Lighthouse\Support\Http\Controllers\GraphQLController;
use Radic\GraphqlStreamsApiModule\GraphQL\Schema\Context;

class ApiGraphQLController extends Controller
{
    /** @var string */
    protected $query;

    /** @var array */
    protected $variables;

    /** @var \Nuwave\Lighthouse\GraphQL */
    protected $graphQL;


    /**
     * Inject middleware into request.
     *
     * @param Request            $request
     * @param ExtensionRegistry  $extensionRegistry
     * @param MiddlewareRegistry $middlewareRegistry
     * @param GraphQL            $graphQL
     */
    public function __construct(Request $request, ExtensionRegistry $extensionRegistry, MiddlewareRegistry $middlewareRegistry, GraphQL $graphQL)
    {
        $this->graphQL = $graphQL;

        $this->query     = $request->input('query');
        $this->variables = \is_string($variables = $request->input('variables'))
            ? json_decode($variables, true)
            : $variables;

        $extensionRegistry->requestDidStart(new ExtensionRequest($request, $this->query, $this->variables));

        $this->graphQL->prepSchema();

        $this->middleware(
            $middlewareRegistry->forRequest($this->query)
        );
    }


    /**
     * Execute GraphQL query.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function query(Request $request)
    {
        $debug = config('app.debug')
            ? config('lighthouse.debug')
            : false;

        $user    = app()->bound('auth') ? auth()->user() : null;
        $context = new Context($request, $user);
        $result  = $this->graphQL
            ->executeQuery($this->query, $context, $this->variables)
            ->toArray($debug);
        return response($result);
    }
}