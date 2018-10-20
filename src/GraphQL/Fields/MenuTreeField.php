<?php

namespace Radic\GraphqlStreamsApiModule\GraphQL\Fields;

use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Radic\GraphqlStreamsApiModule\Command\GetFullMenu;

class MenuTreeField
{
    use DispatchesJobs;

    public function resolve($root, array $args, $context, ResolveInfo $info)
    {
        $menu = $this->dispatch(new GetFullMenu(data_get($args, 'identifier')));

        return $menu;
    }
}