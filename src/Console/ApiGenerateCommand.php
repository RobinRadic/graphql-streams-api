<?php

namespace Radic\GraphqlStreamsApiModule\Console;

use Anomaly\Streams\Platform\Addon\AddonCollection;
use Anomaly\Streams\Platform\Application\Application;
use Anomaly\Streams\Platform\Stream\Contract\StreamRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Radic\GraphqlStreamsApiModule\Generator\Generator;
use Radic\GraphqlStreamsApiModule\Support\FileWatcher;

class ApiGenerateCommand extends Command
{
    use DispatchesJobs;

    protected $signature = 'api:generate {--watch}';

    /** @var Generator */
    protected $generator;

    protected $outputFilePath;

    protected $importFilePaths;

    /** @var \Anomaly\Streams\Platform\Addon\Addon */
    protected $addon;

    /**
     * handle method
     *
     * @param \Radic\GraphqlStreamsApiModule\Generator\Generator $generator
     *
     * @param \Radic\GraphqlStreamsApiModule\Support\FileWatcher $watcher
     *
     * @return void
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle(Generator $generator, FileWatcher $watcher, AddonCollection $addons, Application $application)
    {
        $this->generator       = $generator;
        $this->outputFilePath  = realpath(config('radic.module.graphql_streams_api::config.schema.output'));
        $this->importFilePaths = config('radic.module.graphql_streams_api::config.schema.imports');

        $this->generate();

        if ( ! $this->option('watch')) {
            return;
        }


        $this->addon     = $addons->get('radic.module.graphql_streams_api');
        $unpublishedPath = $this->addon->getPath('resources');
        $publishedPath   = $application->getResourcesPath(
            'addons/' .
            $this->addon->getVendor() . '/' .
            $this->addon->getSlug() . '-' .
            $this->addon->getType()
        );
        $isPublished     = starts_with($this->outputFilePath, $publishedPath) && is_dir($publishedPath);
        $watchPath       = $isPublished ? $publishedPath : $unpublishedPath;

        $this->info('Watching for changes...');
        $watcher
            ->modified(function ($file) {
                if ($file === $this->outputFilePath) {
                    return;
                }
                $this->generate();
            })
            ->watch($watchPath, '*/*.{*}');
    }

    /**
     * generate method
     *
     * @return void
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function generate()
    {
        $this->generator->generateSchemaFile($this->outputFilePath, $this->importFilePaths);
        $this->info("Generated scheme file [{$this->outputFilePath}]");
    }

}