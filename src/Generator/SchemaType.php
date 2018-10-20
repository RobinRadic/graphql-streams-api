<?php

namespace Radic\GraphqlStreamsApiModule\Generator;

class SchemaType extends SchemaNode
{
    use Concerns\HasDirectives;
    use Concerns\HasFields;

    public function __construct(string $name = null)
    {
        parent::__construct();
        $this->setName($name);
    }

    public function fromArray(array $array = [])
    {
        parent::fromArray($array);
        foreach (data_get($array, 'directives', []) as $directiveData) {
            $this->addDirective(
                $this->getBuilder()
                    ->makeDirective(data_get($directiveData, 'name'))
                    ->fromArray($directiveData)
            );
        }
        foreach (data_get($array, 'fields', []) as $fieldData) {
            $this->addField(
                $this->getBuilder()
                    ->makeField(data_get($fieldData, 'name'))
                    ->fromArray($fieldData)
            );
        }
        return $this;
    }
    public function toString()
    {
        $segments = [
            'type ',
            $this->getName(),
        ];

        foreach ($this->getDirectives() as $directive) {
            $segments[] = ' ';
            $segments[] = $directive->toString();
        }
        $segments[] = " {\n";
        foreach ($this->getFields() as $field) {
            $segments[] = "\t" . $field->toString() . "\n";
        }
        $segments[] = '}';

        return implode('', $segments);
    }
}