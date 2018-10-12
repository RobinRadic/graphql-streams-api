<?php

namespace Radic\GraphqlStreamsApiModule\GraphQL\Directives;

use GraphQL\Error\Error;
use GraphQL\Language\AST\FieldDefinitionNode;
use GraphQL\Language\AST\ObjectTypeDefinitionNode;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Nuwave\Lighthouse\Execution\QueryUtils;
use Nuwave\Lighthouse\Schema\AST\ASTHelper;
use Nuwave\Lighthouse\Schema\AST\DocumentAST;
use Nuwave\Lighthouse\Schema\AST\PartialParser;
use Nuwave\Lighthouse\Schema\Directives\Fields\PaginateDirective;
use Radic\GraphqlStreamsApiModule\GraphQL\QueryablePaginateBuilder;

class QueryablePaginateDirective extends PaginateDirective
{
    /**
     * Name of the directive.
     *
     * @return string
     */
    public function name(): string
    {
        return 'queryablePaginate';
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
     * @throws \Exception
     */
    public function manipulateSchema(FieldDefinitionNode $fieldDefinition, ObjectTypeDefinitionNode $parentType, DocumentAST $current): DocumentAST
    {
        $current                                       = parent::manipulateSchema($fieldDefinition, $parentType, $current);
        $connectionArguments                           = PartialParser::inputValueDefinitions([
            'count: Int = 0',
            'page: Int = 1',
            'query: QueryConstraints',
        ]);
        $connectionArguments[ 0 ]->defaultValue->value = 0;
        $connectionArguments[ 1 ]->defaultValue->value = 1;

        $fieldDefinition->arguments = ASTHelper::mergeNodeList($fieldDefinition->arguments, $connectionArguments);
        $parentType->fields         = ASTHelper::mergeNodeList($parentType->fields, [ $fieldDefinition ]);
        $current->setDefinition($parentType);
        return $current;
    }


    /**
     * getPaginatatedResults method
     *
     * @param array $resolveArgs
     * @param int   $page
     * @param int   $first
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * @throws \GraphQL\Error\Error
     * @throws \Nuwave\Lighthouse\Exceptions\DirectiveException
     */
    protected function getPaginatatedResults(array $resolveArgs, int $page, int $first): LengthAwarePaginator
    {
        $builderCallback = $this->directiveArgValue('builder', QueryablePaginateBuilder::class . '@resolve');
        $builder         = $this->getMethodCallback($builderCallback);
        $resolveArgs[]   = $this->directiveArgValue('model');

        $query = \call_user_func_array($builder, $resolveArgs);
        $args  = $resolveArgs[ 1 ];
        $query = QueryUtils::applyFilters($query, $args);
        $query = QueryUtils::applyScopes($query, $args, $this->directiveArgValue('scopes', []));

        return $query->paginate($first, [ '*' ], 'page', $page);
    }

    /**
     * getMethodCallback method
     *
     * @param $classAtMethod
     *
     * @return \Closure
     * @throws \GraphQL\Error\Error
     * @throws \Nuwave\Lighthouse\Exceptions\DirectiveException
     */
    protected function getMethodCallback($classAtMethod)
    {
        $argumentParts = explode('@', $classAtMethod);

        if (
            count($argumentParts) !== 2
            || empty($argumentParts[ 0 ])
            || empty($argumentParts[ 1 ])
        ) {
            throw new Error("Callback '{$classAtMethod}' is not valid");
        }

        $className  = $this->namespaceClassName($argumentParts[ 0 ]);
        $methodName = $argumentParts[ 1 ];

        if ( ! method_exists($className, $methodName)) {
            throw new Error("Callback '{$classAtMethod}' does not exist on class '{$className}'");
        }

        return \Closure::fromCallable([ resolve($className), $methodName ]);
    }
}