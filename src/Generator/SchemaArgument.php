<?php

namespace Radic\GraphqlStreamsApiModule\Generator;

class SchemaArgument extends SchemaNode
{
    use Concerns\HasDirectives;
    use Concerns\HasType;

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
        $this->setType(data_get($array, 'type.name', data_get($array, 'type')));
        $this->setIsList(data_get($array, 'type.is_list', false));
        $this->setIsNonNull(data_get($array, 'type.is_non_null', false));
        $this->setIsListNonNull(data_get($array, 'type.is_list_non_null', false));
        return $this;
    }


    public function toString()
    {
        $segments = [
            $this->getName(),
            ': ',
            $this->getTypeString(),
        ];
        foreach ($this->getDirectives() as $directive) {
            $segments[] = $directive->toString();
        }
        return implode('', $segments);
    }
}