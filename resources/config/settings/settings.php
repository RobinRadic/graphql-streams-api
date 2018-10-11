<?php

return [
    'prefix' => [

        'env'    => 'GRAPHQL_STREAMS_API_PREFIX',
        'type'   => 'anomaly.field_type.text',
        'bind'   => 'radic.module.graphql_streams_api::config.endpoint.prefix',
        'config' => [
            'default_value' => '',
        ],
    ],
];
