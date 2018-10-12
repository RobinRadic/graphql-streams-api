<?php

namespace Radic\GraphqlStreamsApiModule\GraphQL;

use GraphQL\Error\Error;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Database\Query\Builder;

class QueryablePaginateBuilder
{
    /** @var Builder */
    protected $query;

    /**
     * posts method
     *
     * @param                                      $root
     * @param array                                $args
     * @param                                      $context
     * @param \GraphQL\Type\Definition\ResolveInfo $resolveInfo
     * @param string|null                          $model
     *
     * @return \Illuminate\Database\Query\Builder
     * @throws \GraphQL\Error\Error
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, string $model = null)
    {
        $where   = data_get($args, 'query.where');
        $orderBy = data_get($args, 'query.orderBy');

        $this
            ->setQueryByModel($model)
            ->applyWhere($where)
            ->applyOrderBy($orderBy);

        return $this->query;
    }

    protected function setQueryByModel($model)
    {
        /** @var \Anomaly\Streams\Platform\Entry\EntryModel $model */
        $model       = app()->make($model);
        $this->query = $model->newQuery();
        return $this;
    }

    protected function applyWhere($where)
    {
        if ($where === null || ! \is_array($where)) {
            return $this;
        }
        if (array_key_exists(0, $where)) {
            foreach ($where as $item) {
                $this->applyWhere($item);
            }
            return $this;
        }

        $column   = data_get($where, 'column');
        $operator = data_get($where, 'operator', '=');
        $value    = data_get($where, 'value');
        $boolean  = data_get($where, 'boolean', 'and');

        if (\is_object($value) && property_exists($value, 'value')) {
            $value = $value->value;
        }

        $this->query->where($column, $operator, $value, $boolean);
        return $this;
    }

    /**
     * applyOrderBy method
     *
     * @param $orderBy
     *
     * @return $this
     * @throws \GraphQL\Error\Error
     */
    protected function applyOrderBy($orderBy)
    {
        if ($orderBy === null || ! \is_array($orderBy)) {
            return $this;
        }
        if (array_key_exists(0, $orderBy)) {
            foreach ($orderBy as $item) {
                $this->applyOrderBy($item);
            }
            return $this;
        }

        $column = data_get($orderBy, 'column');
        $order  = strtolower(data_get($orderBy, 'order', 'asc'));
        if ($order !== 'asc' && $order !== 'desc') {
            throw new Error("Invalid order [{$order}] for column [{$column}]");
        }
        $this->query->orderBy($column, $order);

        return $this;
    }
}