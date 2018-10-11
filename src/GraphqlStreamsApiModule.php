<?php namespace Radic\GraphqlStreamsApiModule;

use Anomaly\Streams\Platform\Addon\Module\Module;

class GraphqlStreamsApiModule extends Module
{

    /**
     * The navigation display flag.
     *
     * @var bool
     */
    protected $navigation = true;

    /**
     * The addon icon.
     *
     * @var string
     */
    protected $icon = 'fa fa-puzzle-piece';

    /**
     * The module sections.
     *
     * @var array
     */
    protected $sections = [
        'overview'       => [
            'slug' => 'overview',
            'href' => 'admin/graphql_streams_api',
        ],
    ];

}
