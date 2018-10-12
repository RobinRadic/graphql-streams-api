<?php

namespace Radic\GraphqlStreamsApiModule\GraphQL\Directives;

use GraphQL\Language\AST\FieldDefinitionNode;
use GraphQL\Language\AST\ObjectTypeDefinitionNode;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Exceptions\DirectiveException;
use Nuwave\Lighthouse\Schema\AST\ASTHelper;
use Nuwave\Lighthouse\Schema\AST\DocumentAST;
use Nuwave\Lighthouse\Schema\AST\PartialParser;
use Nuwave\Lighthouse\Schema\Values\FieldValue;
use Nuwave\Lighthouse\Support\Contracts\FieldManipulator;
use Nuwave\Lighthouse\Support\Contracts\FieldResolver;

class QueryableDirective extends BaseDirective implements FieldManipulator, FieldResolver
{
    static public $defaultResolver = self::class . '@resolveConstraints';

    public function name()
    {
        return 'queryable';
    }

    /**
     * manipulateSchema method
     *
     * @param \GraphQL\Language\AST\FieldDefinitionNode      $fieldDefinition
     * @param \GraphQL\Language\AST\ObjectTypeDefinitionNode $parentType
     * @param \Nuwave\Lighthouse\Schema\AST\DocumentAST      $current
     *
     * @return \Nuwave\Lighthouse\Schema\AST\DocumentAST
     * @throws \Nuwave\Lighthouse\Exceptions\DocumentASTException
     */
    public function manipulateSchema(FieldDefinitionNode $fieldDefinition, ObjectTypeDefinitionNode $parentType, DocumentAST $current)
    {
        $connectionArguments                           = PartialParser::inputValueDefinitions([
            'query: QueryConstraints',
        ]);

        $fieldDefinition->arguments = ASTHelper::mergeNodeList($fieldDefinition->arguments, $connectionArguments);
        $parentType->fields         = ASTHelper::mergeNodeList($parentType->fields, [ $fieldDefinition ]);
        $current->setDefinition($parentType);
        return $current;
    }

    public function resolveField(FieldValue $value)
    {
        $directiveResolver = null;
        try {
            $directiveResolver = $this->getResolver();
        }
        catch (DirectiveException $e) {
            list($class, $method) = explode('@', static::$defaultResolver);
            $directiveResolver = \Closure::fromCallable([ app($class), $method ]);
        }

        return $value->setResolver(function ($root, array $args, $context = null, ResolveInfo $info = null) use ($directiveResolver) {
            $query = data_get($args, 'query', null);

            $constraints = $this->makeQueryConstraints($query);

            $resolverReflection = new \ReflectionFunction($directiveResolver);
            $callback           = [ $resolverReflection->getClosureThis(), $resolverReflection->getName() ];

            $value = app()->call($callback, compact('constraints', 'query', 'root', 'args', 'context', 'info'));
            return $value;
        });
    }

    protected function makeQueryConstraints($query)
    {
        return with(new QueryConstraints)->fromQuery($query);
    }
}


