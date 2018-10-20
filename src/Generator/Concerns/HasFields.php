<?php

namespace Radic\GraphqlStreamsApiModule\Generator\Concerns;

use Illuminate\Support\Collection;
use Radic\GraphqlStreamsApiModule\Generator\SchemaField;

trait HasFields
{
    /** @var \Illuminate\Support\Collection|SchemaField[] */
    protected $fields;
    public function bootHasFields()
    {
        $this->fields = new Collection();
    }

    /**
     * getFields method
     *
     * @return \Illuminate\Support\Collection|\Radic\GraphqlStreamsApiModule\Generator\SchemaField[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    public function setFields($fields)
    {
        $this->fields = $fields;
        return $this;
    }

    public function addField(SchemaField $field)
    {
        return $this->setField($field->getName(), $field);
    }

    public function setField($name, SchemaField $field)
    {
        $this->fields->put($name, $field);
        return $this;
    }

    public function getField($name)
    {
        return $this->fields->get($name);
    }

    public function hasField($name)
    {
        return $this->fields->has($name);
    }

}