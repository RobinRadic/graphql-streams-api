<?php

namespace Radic\GraphqlStreamsApiModule\GraphQL\Scalars;

use GraphQL\Type\Definition\ScalarType;

class Assoc extends ScalarType //Type
{
    public $name = 'Assoc';

    public function serialize($value)
    {
        return $value;
    }

    public function parseValue($value)
    {
        return $value;
    }

    public function parseLiteral($valueNode, array $variables = null)
    {
        return $valueNode;
    }
}
