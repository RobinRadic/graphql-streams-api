<?php

namespace Radic\GraphqlStreamsApiModule\Generator\Concerns;

use Illuminate\Support\Collection;
use Radic\GraphqlStreamsApiModule\Generator\SchemaArgument;

trait HasArguments
{
    /** @var \Illuminate\Support\Collection|SchemaArgument[] */
    protected $arguments;

    public function bootHasArguments()
    {
        $this->arguments = new Collection();
    }

    /**
     * getArguments method
     *
     * @return \Illuminate\Support\Collection|\Radic\GraphqlStreamsApiModule\Generator\SchemaArgument[]
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    public function setArguments($arguments)
    {
        $this->arguments = $arguments;
        return $this;
    }

    public function addArgument(SchemaArgument $argument)
    {
        return $this->setArgument($argument->getName(), $argument);
    }

    public function setArgument($name, SchemaArgument $argument)
    {
        $this->arguments->put($name, $argument);
        return $this;
    }

    public function getArgument($name)
    {
        return $this->arguments->get($name);
    }

    public function hasArgument($name)
    {
        return $this->arguments->has($name);
    }
}