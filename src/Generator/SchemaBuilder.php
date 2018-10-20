<?php

namespace Radic\GraphqlStreamsApiModule\Generator;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Collection;

class SchemaBuilder extends SchemaNode
{
    /** @var \Illuminate\Contracts\Container\Container */
    protected $container;

    /** @var \Illuminate\Support\Collection|\Radic\GraphqlStreamsApiModule\Generator\SchemaType[] */
    protected $types;

    public function __construct(Container $container)
    {
        parent::__construct();
        $this->container = $container;
        $this->types     = new Collection();
    }

    public function fromArray(array $types = [])
    {
        foreach ($types as $data) {
            $type = $this->makeType(data_get($data, 'name'), true);
            foreach (data_get($data, 'directives', []) as $directiveData) {
                $type->addDirective(
                    $this
                        ->makeDirective(data_get($directiveData, 'name'))
                        ->fromArray($directiveData)
                );
            }
            foreach (data_get($data, 'fields', []) as $fieldData) {
                $type->addField(
                    $this
                        ->makeField(data_get($fieldData, 'name'))
                        ->fromArray($fieldData)
                );
            }
        }
        return $this;
    }

    public function toString()
    {

        $segments = [];
        foreach ($this->getTypes() as $type) {
            $segments[] = $type->toString();
            $segments[] = "\n";
        }
        return implode('', $segments);
    }

    protected function makeNode($class, $name)
    {
        return $this->container->make($class, compact('name'));
    }

    public function makeArgument($name): SchemaArgument
    {
        return $this->makeNode(SchemaArgument::class, $name);
    }

    public function makeDirective($name): SchemaDirective
    {
        return $this->makeNode(SchemaDirective::class, $name);
    }

    public function makeField($name): SchemaField
    {
        return $this->makeNode(SchemaField::class, $name);
    }


    public function makeType(string $name, bool $addToSchema = false): SchemaType
    {
        $type = $this->makeNode(SchemaType::class, $name);
        if ($addToSchema) {
            $this->addType($type);
        }
        return $type;
    }

    public function setType($name, SchemaType $type)
    {
        $this->types->put($name, $type);
        return $this;
    }

    public function addType(SchemaType $type)
    {
        return $this->setType($type->getName(), $type);
    }

    public function hasType($name)
    {
        return $this->types->has($name);
    }

    public function getType($name)
    {
        return $this->types->get($name);
    }

    public function setTypes($types)
    {
        $this->types = $types;
        return $this;
    }

    public function getTypes()
    {
        return $this->types;
    }

}