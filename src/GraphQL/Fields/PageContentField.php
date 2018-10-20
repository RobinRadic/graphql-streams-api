<?php

namespace Radic\GraphqlStreamsApiModule\GraphQL\Fields;

use GraphQL\Type\Definition\ResolveInfo;

class PageContentField
{
    /**
     * resolve method
     *
     * @param                                          $root
     * @param array                                    $args
     * @param                                          $context
     * @param \GraphQL\Type\Definition\ResolveInfo     $info
     *
     * @return void
     */
    public function resolve($root, array $args, $context, ResolveInfo $info)
    {
        $root->setAttribute('content', $content = $root->content());
//        $root->sorted()
        return $content;
    }
}