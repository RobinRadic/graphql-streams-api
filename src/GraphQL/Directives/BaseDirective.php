<?php

namespace Radic\GraphqlStreamsApiModule\GraphQL\Directives;

abstract class BaseDirective extends \Nuwave\Lighthouse\Schema\Directives\BaseDirective
{
    public function getArguments()
    {
        $arguments = collect(data_get($this->definitionNode, 'arguments', []));
        $arguments = $arguments->keyBy('name.value');
        return $arguments;
    }

    public function hasArgument($key)
    {
        if(! $this->getArguments()->has($key) ){
            return false;
        }
//        $argument = $this->getArguments()->get($key);
        return true;
    }

    public function argument($key, $default = null)
    {
        $argument = $this->getArguments()->get($key, $default);
        $defaultValue = $default;
        if($argument->defaultValue && $argument->defaultValue->value){
            $defaultValue = $argument->defaultValue->value;
        }
        try {
            return $this->argValue($argument, $defaultValue);
        } catch (\Throwable $e){
            return $defaultValue;
        }
    }
}
