<?php

namespace Radic\GraphqlStreamsApiModule\Generator;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Radic\GraphqlStreamsApiModule\Command\GetStreamFields;
use Radic\GraphqlStreamsApiModule\Support\TextBuffer;

/**
 * This is the class Generator.
 *
 * @package Radic\GraphqlStreamsApiModule\Generator
 * @author  Robin Radic
 */
class Generator
{
    use DispatchesJobs;

    /**
     * generateSchemaFile method
     *
     * @param string $outputPath
     * @param array  $importPaths
     *
     * @return bool
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function generateSchemaFile(string $outputPath, array $importPaths = [])
    {
        $fs = new Filesystem();
        if ($fs->exists($outputPath)) {
            $fs->delete($outputPath);
        }
        $fs->put($outputPath, $content = $this->generateSchema($importPaths));
        return true;
    }

    /**
     * generateSchema method
     *
     * @param array $importPaths
     *
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Exception
     */
    public function generateSchema(array $importPaths = [])
    {
        $fs          = new Filesystem();
        $text        = new TextBuffer();
        $streamNames = $this->getSchemeBindingNames();
        foreach ($streamNames as $name) {
            $text->append()->append($this->generateStreamTypeDefinition($name));
        }
        foreach ($importPaths as $path) {
            $text->append()->append($fs->get($path));
        }
        return $text->toString();
    }

    /**
     * generateStreamTypeDefinition method
     *
     * @param $name
     *
     * @return mixed
     * @throws \Exception
     */
    public function generateStreamTypeDefinition($name)
    {
        $schemeBinding = $this->getSchemeBinding($name);
        list($namespace, $slug) = explode('::', $name);
        $typeName                = $schemeBinding->get('type_name');
        $resolutions             = $schemeBinding->get('resolutions');
        $defaultFieldResolutions = $this->getDefaultFieldResolutions();
        $streamFields            = $this->getStreamFields($namespace, $slug);

        $text = with(new TextBuffer());
        $text
            ->append("# stream: {$namespace}.{$slug}")
            ->append("type {$typeName} {")
            ->indent();

        foreach ($resolutions as $key => $value) {
            $fieldSlug = $key;
            $graphType = $value;
            if (is_numeric($key)) {
                $fieldSlug           = $value;
                $graphType           = null;
                $assignedStreamField = $streamFields->get($fieldSlug, null);
                if ($assignedStreamField && $defaultFieldResolutions->has($assignedStreamField->type)) {
                    $graphType = $defaultFieldResolutions->get($assignedStreamField->type);
                }
            }
            $text->append("{$fieldSlug}: {$graphType}");
        }

        $text->unindent()->append('}');

        return $text->toString();
    }


    /**
     * getDefaultFieldResolutions method
     *
     * @return \Illuminate\Support\Collection
     */
    public function getDefaultFieldResolutions()
    {
        return collect(config('radic.module.graphql_streams_api::config.default_field_resolutions'));
    }

    /**
     * getSchemeBinding method
     *
     * @param $name
     *
     * @return \Illuminate\Config\Repository|\Illuminate\Support\Collection
     * @throws \Exception
     */
    public function getSchemeBinding($name)
    {
        $schemeBinding = config('radic.module.graphql_streams_api::config.stream_bindings.' . $name, null);
        if ($schemeBinding === null) {
            throw new \Exception("Stream binding [{$name}] not found. Make sure its defined in the config [radic.module.graphql_streams_api::config.stream_bindings]");
        }
        $schemeBinding = collect($schemeBinding);
        if ( ! $schemeBinding->has('type_name') || ! $schemeBinding->has('resolutions')) {
            throw new \Exception("Stream binding [{$name}] is missing [type_name] or [fields]");
        }
        return $schemeBinding;
    }

    /**
     * getSchemeBindingNames method
     *
     * @return array
     */
    public function getSchemeBindingNames()
    {
        return array_keys(config('radic.module.graphql_streams_api::config.stream_bindings', []));
    }

    /**
     * getStreamFields method
     *
     * @param $namespace
     * @param $slug
     *
     * @return \Radic\GraphqlStreamsApiModule\Command\Field[]|\Illuminate\Support\Collection
     */
    public function getStreamFields($namespace, $slug)
    {
        return $this->dispatch(new GetStreamFields($namespace, $slug));
    }
}