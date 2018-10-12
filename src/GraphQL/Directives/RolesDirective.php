<?php

namespace Radic\GraphqlStreamsApiModule\GraphQL\Directives;

use Anomaly\UsersModule\User\Contract\UserInterface;
use GraphQL\Error\Error;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Nuwave\Lighthouse\Schema\Values\FieldValue;
use Nuwave\Lighthouse\Support\Contracts\FieldMiddleware;

class RolesDirective extends BaseDirective implements FieldMiddleware
{
    public function name()
    {
        return 'roles';
    }

    public function handleField(FieldValue $value, \Closure $next)
    {
        $resolver = $value->getResolver();

        return $next($value->setResolver(function () use ($resolver) {
            /** @var Authorizable|\Anomaly\UsersModule\User\Contract\UserInterface $user */
            $user = app('auth')->user();

            if ( ! $user) {
                throw new Error('Authentication is required to access this field.');
            }//                    $model = $this->getModelClass();

            $roles    = $this->directiveArgValue('roles', $this->directiveArgValue('role'));
            $roles    = \is_array($roles) ? $roles : [ $roles ];
            $operator = strtoupper($this->directiveArgValue('operator', 'OR'));
            if ($operator !== 'OR' && $operator !== 'AND' && $operator !== 'NOT') {
                throw new Error('Invalid operator [' . $operator . '] for allowedRoles. Available: "OR" || "AND" || "NOT"');
            }

            if ( ! $this->checkUserRoles($user, $roles, $operator)) {
                throw new Error('You do not have the required permissions to access this field');
            }

            return call_user_func_array($resolver, func_get_args());
        }));
    }

    protected function checkUserRoles(UserInterface $user, array $roles, string $operator)
    {
        if ($operator === 'OR') {
            return $user->hasAnyRole($roles);
        }
        if ($operator === 'AND') {
            $array_filter = [];
            foreach ($roles as $key => $role) {
                if ($user->hasRole($role)) {
                    $array_filter[ $key ] = $role;
                }
            }
            return count($array_filter) === 0;
        }
        if ($operator === 'NOT') {
            return ! $user->hasAnyRole($roles);
        }
        return false;
    }
}