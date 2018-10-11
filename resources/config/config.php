<?php


return [
    'endpoint'                  => [
        'prefix'     => '',
        'middleware' => [],
    ],
    'schema'                    => [
        'output'     => __DIR__ . '/../schema/schema.graphqls',
        'imports'    => [
            __DIR__ . '/../schema/core.graphqls',
        ],
        'directives' => [
            \Radic\GraphqlStreamsApiModule\GraphQL\Directives\RolesDirective::class
        ],
    ],
    'default_field_resolutions' => [
        'anomaly.field_type.addon'       => 'String',
        'anomaly.field_type.blocks'      => 'String',
        'anomaly.field_type.boolean'     => 'Boolean',
        'anomaly.field_type.checkboxes'  => 'Boolean',
        'anomaly.field_type.colorpicker' => 'String',
        'anomaly.field_type.country'     => 'String',
        'anomaly.field_type.datetime'    => 'DateTimeTz',
        'anomaly.field_type.decimal'     => 'Int',
        'anomaly.field_type.editor'      => 'String',
        'anomaly.field_type.email'       => 'String',
        'anomaly.field_type.encrypted'   => 'String',
        'anomaly.field_type.file'        => 'String',
        'anomaly.field_type.files'       => 'String',
        'anomaly.field_type.geocoder'    => 'String',
        'anomaly.field_type.grid'        => 'String',
        'anomaly.field_type.integer'     => 'Int',
        'anomaly.field_type.language'    => 'String',
        'anomaly.field_type.markdown'    => 'String',
//        'anomaly.field_type.multiple'     => 'String',
//        'anomaly.field_type.polymorphic'  => 'String',
//        'anomaly.field_type.relationship' => 'String',
        'anomaly.field_type.repeater'    => 'String',
        'anomaly.field_type.select'      => 'String',
        'anomaly.field_type.slider'      => 'String',
        'anomaly.field_type.slug'        => 'String',
        'anomaly.field_type.state'       => 'String',
        'anomaly.field_type.tags'        => 'String',
        'anomaly.field_type.text'        => 'String',
        'anomaly.field_type.textarea'    => 'String',
        'anomaly.field_type.url'         => 'String',
        'anomaly.field_type.wysiwyg'     => 'String',
    ],
    'stream_bindings'           => [
        'users::users'                 => [
            'type_name'   => 'User',
            'resolutions' => [
                'id'         => 'ID',
                'roles'      => '[UserRole!] @hasMany',
                'created_at' => 'Date',
                'updated_at' => 'Date',
                'deleted_at' => 'Date',
                'email',
                'username',
                'display_name',
                'first_name',
                'last_name',
                'activated',
                'enabled',
            ],
        ],
        'users::roles'                 => [
            'type_name'   => 'UserRole',
            'resolutions' => [
                'id' => 'ID',
                'name',
                'slug',
                'description',
                'permissions',
            ],
        ],
        'pages::pages'                 => [
            'type_name'   => 'Page',
            'resolutions' => [
                'id'           => 'ID',
                'title',
                'slug',
                'path',
                'type'         => 'PageType @belongsTo',
                'parent'       => 'Page @belongsTo',
                'children'     => '[Page!] @hasMany',
                'siblings'     => '[Page!] @hasMany',
                'visible',
                'enabled',
                'exact',
                'home',
                'meta_title',
                'meta_description',
                'themeLayout'  => 'String',
                'allowedRoles' => '[UserRole!] @hasMany',
            ],
        ],
        'pages::types'                 => [
            'type_name'   => 'PageType',
            'resolutions' => [
                'id' => 'ID',
                'name',
                'slug',
                'description',
            ],
        ],
        'navigation::menus'            => [
            'type_name'   => 'NavigationMenu',
            'resolutions' => [
                'name',
                'slug',
                'description',
                'links' => '[NavigationLink!] @hasMany',
            ],
        ],
        'navigation::links'            => [
            'type_name'   => 'NavigationLink',
            'resolutions' => [
                'menu'         => 'NavigationMenu @belongsTo',
                'children'     => '[NavigationLink!] @hasMany',
                'target',
                'class',
                'parent'       => 'NavigationLink @belongsTo',
                'allowedRoles' => '[UserRole!] @hasMany',
            ],
        ],
        'configuration::configuration' => [
            'type_name'   => 'Configuration',
            'resolutions' => [
                'scope',
                'key',
                'value',
            ],
        ],
        'blocks::blocks'               => [
            'type_name'   => 'Block',
            'resolutions' => [
                'title',
                'area'         => 'BlockArea @belongsTo',
                'extension'    => 'Assoc',
                'displayTitle' => 'Boolean'
//                'field' => 'BlockArea @hasOne',
            ],
        ],
        'blocks::areas'                => [
            'type_name'   => 'BlockArea',
            'resolutions' => [
                'name',
                'slug',
                'description',
            ],
        ],
    ],
];