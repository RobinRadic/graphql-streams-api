<?php

namespace Radic\GraphqlStreamsApiModule\Generator\Concerns;

use Illuminate\Support\Collection;
use Radic\GraphqlStreamsApiModule\Generator\SchemaDirective;

trait HasDirectives
{
    /** @var \Illuminate\Support\Collection|SchemaDirective[] */
    protected $directives;

    public function bootHasDirectives()
    {
        $this->directives = new Collection();
    }

    /**
     * getDirectives method
     *
     * @return \Illuminate\Support\Collection|\Radic\GraphqlStreamsApiModule\Generator\SchemaDirective[]
     */
    public function getDirectives()
    {
        return $this->directives;
    }

    public function setDirectives($directives)
    {
        $this->directives = $directives;
        return $this;
    }

    public function addDirective(SchemaDirective $directive)
    {
        return $this->setDirective($directive->getName(), $directive);
    }

    public function setDirective($name, SchemaDirective $directive)
    {
        $this->directives->put($name, $directive);
        return $this;
    }

    public function getDirective($name)
    {
        return $this->directives->get($name);
    }

    public function hasDirective($name)
    {
        return $this->directives->has($name);
    }

}