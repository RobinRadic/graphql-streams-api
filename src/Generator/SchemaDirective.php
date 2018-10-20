<?php

namespace Radic\GraphqlStreamsApiModule\Generator;

class SchemaDirective extends SchemaNode
{
    use Concerns\HasArguments;

    public function __construct(string $name = null)
    {
        parent::__construct();
        $this->setName($name);
    }

    public function fromArray(array $array = [])
    {
        parent::fromArray($array);
        foreach (data_get($array, 'arguments', []) as $argumentData) {
            $this->addArgument(
                $this->getBuilder()
                    ->makeArgument(data_get($argumentData, 'name'))
                    ->fromArray($argumentData)
            );
        }
        return $this;
    }

    public function toString()
    {
        $segments = [
            '@',
            $this->getName(),
        ];
        if ($this->getArguments()->isNotEmpty()) {
            $segments[] = '(';
            foreach ($this->getArguments() as $argument) {
                $segments[] = $argument->toString();
            }
            $segments[] = ') ';
        }
        return implode('', $segments);
    }
}