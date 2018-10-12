<?php

namespace Radic\GraphqlStreamsApiModule\GraphQL\Queries;

use GraphQL\Type\Definition\ResolveInfo;

class Version
{
    public function resolve($rootValue, array $args, $context, ResolveInfo $info)
    {
        return '1.0.0';
    }
}