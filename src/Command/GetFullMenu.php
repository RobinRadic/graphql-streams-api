<?php

namespace Radic\GraphqlStreamsApiModule\Command;

use Anomaly\NavigationModule\Link\Command\GetLinks;
use Anomaly\NavigationModule\Link\LinkCollection;
use Anomaly\NavigationModule\Link\LinkModel;
use Anomaly\NavigationModule\Menu\Command\GetMenu;
use Anomaly\Streams\Platform\Support\Collection;
use Illuminate\Foundation\Bus\DispatchesJobs;

class GetFullMenu
{
    use DispatchesJobs;

    /** @var mixed */
    protected $identifier;

    /**
     * GetFullMenu constructor.
     *
     * @param mixed $identifier
     */
    public function __construct($identifier)
    {
        $this->identifier = $identifier;
    }

    public function handle()
    {
        $options = new Collection();
        $options->put('menu', $this->identifier);
        /** @var \Anomaly\NavigationModule\Menu\Contract\MenuInterface $menu */
        $menu   = $this->dispatch(new GetMenu($options->get('menu')));
        $links  = $this->dispatch(new GetLinks($options, $menu));
        $toTree = function (LinkCollection $links) use (&$toTree) {
            $data = [];
            /** @var LinkModel $link */
            foreach ($links as $link) {
                $link->load('children');
                $type            = $link->getType();
                $entry           = $link->getEntry();
                $item            = $link->toArrayWithRelations();
                $item[ 'entry' ] = $entry->toArrayWithRelations();
                $item[ 'type' ]  = $type->toArray();
                if ( ! $link->getChildren()->isEmpty()) {
                    $item[ 'children' ] = $toTree($link->getChildren());
                }
                $data[] = $item;
            }
            return $data;
        };
        $links  = $toTree($links->top());
        return array_replace($menu->toArray(), [ 'children' => $links ]);
    }


}