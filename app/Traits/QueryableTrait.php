<?php

namespace App\Traits;

use App\Models\Department;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

trait QueryableTrait
{
    public function scopeQueryable($query, $indicator = true)
    {
        $query->when(request()->has('draw'), function (Builder $query) use ($indicator) {
            $columns = request()->input('columns', []);

            // Apply dynamic joins efficiently
            $this->applyDynamicJoins($query, $columns);

            // Select only the necessary columns
            $query->select($this->getSelectColumns($columns, $query));

            // Apply Columns Filters
            $this->applyColumnFilters($query, $columns);

            // Apply Global Filter if needed
            $this->applyFilter($query);

            // Apply Scope if needed
            $this->applyScope($query);

            // Apply Sort if needed
            $this->applySortBy($query);
        });

        $query->when(!request()->has('draw'), function (Builder $query) {
            // Apply Relationship if needed
            $this->applyWith($query);

            // Apply Global Filter if needed
            $this->applyFilter($query);

            // Apply Scope if needed
            $this->applyScope($query);

            // Apply Sort if needed
            $this->applySortBy($query);
        });
    }

    protected function getRelationshipType($model, $relationship)
    {
        // Split relationship path (if nested) to get the final part to inspect
        $relationshipParts = explode('.', $relationship);
        $baseRelation = array_pop($relationshipParts);

        // Traverse the model's relationships to reach the correct nested relationship
        foreach ($relationshipParts as $relationPart) {
            $model = $model->$relationPart()->getRelated();
        }

        // Call the final relationship method dynamically
        $relation = $model->$baseRelation();
        $relationClass = get_class($relation);

        // Return the relationship type based on the class
        switch ($relationClass) {
            case \Illuminate\Database\Eloquent\Relations\HasOne::class:
            case \Illuminate\Database\Eloquent\Relations\BelongsTo::class:
                return 1;
            case \Illuminate\Database\Eloquent\Relations\MorphToMany::class:
                return 2;
            default:
                return abort(422, "Undefined Relationship Type");
        }
    }

    protected function applyDynamicJoins(Builder $query, array $columns)
    {
        foreach ($columns as $column) {
            $relation = $column['relation'] ?? null;

            if ($relation && Str::contains($relation, '.')) {
                $relationship = Str::beforeLast($relation, '.');

                // Dynamically check the relationship type
                $model = $query->getModel();
                $relationshipType = $this->getRelationshipType($model, $relationship);

                if (!isset($relationships[$relationship])) {

                    $count = substr_count($relationship, '.');

                    $relationshipParts = explode('.', $relationship);

                    if ($count >= 1 && $relationshipType == 1) {
                        $relationshipParts = explode('.', $relationship);

                        // Initialize an empty array to store the dynamic join structure
                        $joins = [];

                        // Loop through each part of the relationship
                        foreach ($relationshipParts as $part) {
                            // Create a dynamic join alias for each part
                            $joins[$part] = fn($join) => $join->as($part);
                        }

                        $query->leftJoinRelationship($relationship, $joins);
                    } else if ($relationshipType == 2) {
                        $query->leftJoinRelationship($relationship);
                    } else {
                        $query->leftJoinRelationshipUsingAlias("{$relationship}", "{$relationship}");
                    }
                }

                $relationships[$relationship] = true;
            }
        }

        // dd($query->toSql());
    }

    protected function getSelectColumns(array $columns, Builder $query)
    {
        $baseTable = $query->getModel()->getTable();

        return collect($columns)->map(function ($column) use ($baseTable) {
            $columnName = $column['name'] ?? null;
            $relation = $column['relation'] ?? null;

            if ($relation && Str::contains($relation, '.')) {
                $relationship = Str::beforeLast($relation, '.');
                $field = Str::afterLast($relation, '.');

                $count = substr_count($relationship, '.');
                if ($count >= 1) {
                    $relationship = Str::afterLast($relationship, '.');
                }

                return "{$relationship}.{$field} as {$columnName}";
            }

            // Default to base table columns if no relation is present
            return "{$baseTable}.{$columnName}";
        })->all();
    }

    protected function applyColumnFilters(Builder $query, array $columns)
    {
        // dd($query);
        foreach ($columns as $column) {
            $columnName = $column['relation'] ?? null;
            $searchValue = $column['search']['value'] ?? null;

            if ($columnName && $searchValue) {
                if (Str::contains($columnName, '.')) {
                    $relationship = Str::beforeLast($columnName, '.');
                    $field = Str::afterLast($columnName, '.');

                    $count = substr_count($relationship, '.');
                    if ($count >= 1) {
                        $relationship = Str::afterLast($relationship, '.');
                    }

                    $query->where("{$relationship}.{$field}", 'like', '%' . $searchValue . '%');
                } else {
                    // dd($query->getModel()->getTable() . '.' . $columnName, 'like', '%' . $searchValue . '%');
                    $query->where($query->getModel()->getTable() . '.' . $columnName, 'like', '%' . $searchValue . '%');
                }
            }
        }
    }

    protected function applyWith(Builder $query)
    {
        if (request()->has('with')) {
            $query->with(explode(';', request('with')));
        }
    }

    protected function applyFilter(Builder $query)
    {
        // Apply custom filters from request
        $filters = request()->input('filter', []);

        foreach ($filters as $key => $value) {
            if (!empty($value)) {
                $query->where($key, $value != 'true' ? $value != 'false' ? $value : false : true);
            }
        }
    }

    protected function applyScope(Builder $query)
    {
        $scopes = request()->input('scope');

        if ($scopes) {
            $scopeList = explode(',', $scopes); // Split scopes by comma

            foreach ($scopeList as $scope) {
                $scope = trim($scope); // Trim any whitespace
                $scopeMethod = 'scope' . ucfirst($scope); // Convert scope to method name

                if (method_exists($query->getModel(), $scopeMethod)) {
                    $query->getModel()->$scopeMethod($query);
                }
            }
        }
    }

    protected function applySortBy(Builder $query)
    {
        // Apply custom Sort from request
        $sorts = request()->input('sort', []);

        foreach ($sorts as $key => $value) {
            if (!empty($value)) {
                $query->orderBy($query->getModel()->getTable() . '.' . $key, $value);
            }
        }
    }

    protected function scopeExtendPaginate(Builder $query)
    {
        if (request()->has('no-paginate')) {
            return response()->json([
                'message' => "Berjaya",
                'data' => $query->get(),
            ], 200);
        } else {
            $perPage = request()->input('per_page') ?? 15;

            return $query->paginate($perPage);
        }
    }
}