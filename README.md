# PyroCMS GraphQL Streams Api Module

This module aims to provide an easy yet flexiable way to bind your PyroCMS Streams to a GraphQL API.
This module integrates `nuwave/lighthouse` with PyroCMS Streams.

Please make sure you have a basic understanding of:
- [GraphQL](https://graphql.org/learn/)
- [webonyx/graphql-php](http://webonyx.github.io/graphql-php/getting-started/)
- [Lighthouse](https://lighthouse-php.netlify.com)

## Preview
A small usage preview of this module

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
                'email',
                'username',
                'display_name',
                'first_name',
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

*Todo... In the meanwhile, just check the configuration file. It's pretty much self-explaining.*

## License

Copyright 2018 - Robin Radic - [MIT License](./LICENSE.md)