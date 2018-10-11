<?php

namespace Radic\GraphqlStreamsApiModule\Console;

use Anomaly\Streams\Platform\Stream\Contract\StreamRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Radic\GraphqlStreamsApiModule\Generator\Generator;

class GenerateApiCommand extends Command
{
    use DispatchesJobs;

    protected $signature = 'api:generate';

    /**
     * handle method
     *
     * @param \Radic\GraphqlStreamsApiModule\Generator\Generator $generator
     *
     * @return void
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle(Generator $generator)
    {
        $generator->generateSchemaFile(
            $output = realpath(config('radic.module.graphql_streams_api::config.schema.output')),
            config('radic.module.graphql_streams_api::config.schema.imports')
        );
        $this->info("Generated scheme file [{$output}]");
    }

}