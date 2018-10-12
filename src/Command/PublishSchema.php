<?php

namespace Radic\GraphqlStreamsApiModule\Command;

use Anomaly\Streams\Platform\Addon\Addon;
use Anomaly\Streams\Platform\Application\Application;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;


class PublishSchema
{
    /**
     * The addon instance.
     *
     * @var Addon
     */
    protected $addon;

    /**
     * The console command.
     *
     * @var Command
     */
    protected $command;

    /**
     * Create a new PublishConfig instance.
     *
     * @param Addon   $addon
     * @param Command $command
     */
    public function __construct(Addon $addon, Command $command)
    {
        $this->addon   = $addon;
        $this->command = $command;
    }

    /**
     * Handle the command.
     *
     * @param  Filesystem  $filesystem
     * @param  Application $application
     * @return string
     */
    public function handle(Filesystem $filesystem, Application $application)
    {
        $destination = $application->getResourcesPath(
            'addons/' .
            $this->addon->getVendor() . '/' .
            $this->addon->getSlug() . '-' .
            $this->addon->getType() . '/schema'
        );

        if (is_dir($destination) && !$this->command->option('force')) {

            $this->command->error("$destination already exists.");

            return;
        }

        $filesystem->copyDirectory($this->addon->getPath('resources/schema'), $destination);

        $this->command->info("Published $destination");
    }
}
