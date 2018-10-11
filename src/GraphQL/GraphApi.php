<?php

namespace Radic\GraphqlStreamsApiModule\GraphQL;

use Nuwave\Lighthouse\GraphQL;
use Radic\GraphqlStreamsApiModule\Generator\Generator;

class GraphApi extends GraphQL
{
    /**
     * Execute GraphQL query.
     *
     * @param string $query
     * @param mixed  $context
     * @param array  $variables
     * @param mixed  $rootValue
     *
     * @return array
     */
    public function execute(string $query, $context = null, $variables = [], $rootValue = null): array
    {
        $result = $this->queryAndReturnResult($query, $context, $variables, $rootValue);
        $result->setErrorsHandler([ $this->exceptionHandler(), 'handler' ]);

        $data = $result->toArray();

        if ( ! isset($data[ 'extensions' ])) {
            $data[ 'extensions' ] = [];
        }

        return $data;
    }

    public function getGenerator()
    {
        return new Generator();
    }

}