<?php

namespace Radic\GraphqlStreamsApiModule\Console;

use Anomaly\Streams\Platform\Addon\AddonCollection;
use Anomaly\Streams\Platform\Addon\Console\Command\PublishConfig;
use Anomaly\Streams\Platform\Addon\Console\Command\PublishTranslations;
use Anomaly\Streams\Platform\Addon\Console\Command\PublishViews;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Radic\GraphqlStreamsApiModule\Command\PublishSchema;

class ApiPublishCommand extends Command
{
    use DispatchesJobs;

    protected $signature = 'api:publish';

    public function handle(AddonCollection $addons)
    {

        $addon = $addons->get('radic.module.graphql_streams_api');

        $this->dispatch(new PublishViews($addon, $this));
        $this->dispatch(new PublishConfig($addon, $this));
        $this->dispatch(new PublishTranslations($addon, $this));
        $this->dispatch(new PublishSchema($addon, $this));
    }

}