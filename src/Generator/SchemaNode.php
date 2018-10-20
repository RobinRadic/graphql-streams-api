<?php

namespace Radic\GraphqlStreamsApiModule\Generator;

abstract class SchemaNode
{
    use Concerns\HasName;

    protected $booted = false;

    /** @var \Radic\GraphqlStreamsApiModule\Generator\SchemaBuilder */
    protected $builder;

    public function __construct()
    {
        if ( ! $this instanceof SchemaBuilder) {
            $this->builder = app()->make(SchemaBuilder::class);
        }
        $this->bootIfNotBooted();
    }

    protected function bootIfNotBooted()
    {
        if ($this->booted === false) {
            $this->boot($this);
        }
    }

    protected function boot(SchemaNode $self)
    {
        $this->bootTraits($self);
        $this->booted = true;
    }

    protected function bootTraits(SchemaNode $self)
    {
        $class = static::class;
        foreach (class_uses_recursive($class) as $trait) {
            if (method_exists($class, $method = 'boot' . class_basename($trait))) {
                $self->$method();
            }
        }
    }

    public function __wakeup()
    {
        $this->bootIfNotBooted();
    }

    abstract public function toString();

    public function __toString()
    {
        return $this->toString();
    }

    /**
     * @return \Radic\GraphqlStreamsApiModule\Generator\SchemaBuilder
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * Set the builder value
     *
     * @param \Radic\GraphqlStreamsApiModule\Generator\SchemaBuilder $builder
     *
     * @return SchemaNode
     */
    public function setBuilder($builder)
    {
        $this->builder = $builder;
        return $this;
    }

    public function fromArray(array $array = [])
    {
        $this->setName(data_get($array, 'name'));
        return $this;
    }
}