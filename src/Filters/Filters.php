<?php

namespace App\Filters;

use Illuminate\Http\Request;

abstract class Filters
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * The Eloquent builder.
     *
     * @var \Illuminate\Database\Eloquent\Builder
     */
    protected $builder;

    /**
     * Registered filters to operate upon.
     *
     * @var array
     */
    protected $filters = [];

    /**
     * Create a new ThreadFilters instance.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Apply the filters.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply($builder)
    {
        $this->builder = $builder;

        foreach ($this->getFilters() as $filter => $value) {
            if (method_exists($this, $filter)) {
                $this->$filter($value);
            }
        }

        if ($this->request->filled('orderBy')) {
            $this->orderBy($this->request->orderBy);
        }

        return $this->builder;
    }

    /**
     * Fetch all relevant filters from the request.
     *
     * @return array
     */
    public function getFilters()
    {
        return array_filter($this->request->only($this->filters), function ($item) {
            return ! is_null($item);
        });
    }

    /**
     * Order the query by givens orders.
     *
     * @param $orders
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function orderBy($orders)
    {
        if (! is_array($orders)) {
            $orders = json_decode($orders, true);
        }

        return $this->builder->when(! empty($orders), function ($query) use ($orders) {
            foreach ($orders as $key => $order) {
                $query->orderBy($key, $order);
            }
        });
    }
}
