# PyroCMS GraphQL Streams Api Module

- [Introduction](#introduction)
- [Preview](#preview)
- [Documentation](#documentation)
    - [Installation](#installation)
    - [Customization](#customization)
    - [Generate](#generate)
- [License](#license)


## Introduction
This module aims to provide an easy yet flexiable way to bind your PyroCMS Streams to a GraphQL API.
This module integrates `nuwave/lighthouse` with PyroCMS Streams.

Please make sure you have a basic understanding of:
- [GraphQL](https://graphql.org/learn/)
- [webonyx/graphql-php](http://webonyx.github.io/graphql-php/getting-started/)
- [Lighthouse](https://lighthouse-php.netlify.com)

## Preview
A small usage preview of this module.

### 1. Configure the bindings

**config.php**
```php
return [
    // other config options redacted, check out the documentation for a full overview
    'default_field_resolutions' => [
        'anomaly.field_type.addon'       => 'String',
        'anomaly.field_type.blocks'      => 'String',
        'anomaly.field_type.boolean'     => 'Boolean',
        'anomaly.field_type.checkboxes'  => 'Boolean',
        'anomaly.field_type.colorpicker' => 'String',
        //etc...
    ],
    'stream_bindings'           => [
        // namespace::slug
        'users::users'                 => [

            // generator results: type User {}
            'type_name'   => 'User',

            // PyroCMS Stream field assignment => GraphQL field type
            'resolutions' => [
                // custom type resolutions
                'id'         => 'ID',
                'roles'      => '[UserRole!] @hasMany',
                'created_at' => 'Date',
                'updated_at' => 'Date',
                'deleted_at' => 'Date',

                // The fields below will be resolved using the `default_field_resolutions`
                // If you want, you can assign a custom type resolutions like above
                'username',
                'display_name',
                'first_name',

                // protect fields by requiring authentication and permissions
                'email' => 'String @role(roles: ["admin"], operator: "AND")',
                'email' => 'String @role(roles: ["user", "guest"], operator: "NOT")',
                'email' => 'String @role(roles: ["admin", "user"], operator: "OR")',
                'last_name',
                'activated',
                'enabled',
            ],
        ],
        // other streams...
    ]
];

```

### 2. Generate the schema.graphql

When you have defined all stream bindings and resolutions to your liking then you need to run the generator.
This will generate a GraphQL schema definition file that will be used by Lighthouse:

```
php artisan api:generate
```

### 3. And voila!
![GraphQL Query and Result](https://github.com/RobinRadic/graphql-streams-api/raw/master/screenshot.png)


## Documentation

### Installation

**Add to composer**
```
composer require radic/graphql_streams_api-module
```

**Install addon**
```
php artisan addon:install radic.module.graphql_streams_api
```

**Publish the addon**

To customize and define your GraphQL service use the `api:publish` command.

This will publish the configuration and schema files to `resources/{application}/addons/radic/graphql_streams_api`.

```
php artisan api:publish
```

### Customization
You can customize/define the GraphQL service in `resources/{application}/addons/radic/graphql_streams_api`:
- `config/config.php`
- `schema/queryable.graphqls`
- `schema/core.graphqls`

#### config.schema
- `output`  : The path where the schema will be generated.
- `imports` : Paths to other schema definition files that will be included in the generated schema output.

#### config.stream_bindings
This defines the streams that you want to be generated as GraphQL types.
```php
'stream_bindings' => [
    // '{namespace}::{slug}'
    'users::users' => [
        'type_name'   => 'User', // The resulting GraphQL schema type name.
        'resolutions' => [
            'slug',
            'title',
            'roles' => '[UserRole!] @hasMany',
        ]
    ],
    'users::roles' => [
        'type_name'   => 'UserRole',
        'resolutions' => [
            'slug'
        ]
    ]
]
```

The `resolutions` are used to define the streams fields that should be in the GraphQL type.
You can either:
- Define only stream field name (like 'slugs'). The generator will use the `default_field_bindings` to resolve the GraphQL type automatically.
- Define the stream field name and a custom GraphQL field type. This is usually done with relationship fields or if you want to add some directive(s) to the field.

### Generate
After modifying the `config.php` or any of the defined `schema.imports` files you need to generate the output schema by running
```
php artisan api:generate
```

This will generate the schema in the configured `schema.output` file path.


## License

Copyright 2018 - Robin Radic - [MIT License](./LICENSE.md)