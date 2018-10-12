<?php

namespace Radic\GraphqlStreamsApiModule\GraphQL\Directives;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective as LighthouseBaseDirective;
use Nuwave\Lighthouse\Schema\Values\FieldValue;
use Nuwave\Lighthouse\Support\Contracts\FieldMiddleware;

class DefaultValueDirective extends LighthouseBaseDirective implements FieldMiddleware
{
    public function name()
    {
        return 'default';
    }

    /**
     * Resolve the field directive.
     *
     * @param FieldValue $value
     * @param \Closure   $next
     *
     * @return FieldValue
     */
    public function handleField(FieldValue $value, \Closure $next)
    {
        $valueResolver = $value->getResolver();
        return $next($value->setResolver(function ($root, array $args, $context = null, ResolveInfo $info = null) use ($valueResolver) {
            $defaultValue = $this->directiveArgValue('value');
            try {
                $value = $valueResolver(...func_get_args());
            }
            catch (\Exception $e) {

            }
            if ( ! isset($value)) {
                $value = $defaultValue;
            }

            return $value;
        }));
    }
}


