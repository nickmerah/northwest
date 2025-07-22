<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait HasDynamicIncludes
{
    /**
     * Apply includes (relations) from query param and include their fields dynamically
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithDynamicIncludes($query, Request $request)
    {
        $includes = explode(',', $request->query('include', ''));



        if (!$includes || $includes === ['']) {
            return $query;
        }

        $model = $query->getModel();

        // Filter only those includes which have a method on model and returns a Relation
        $validIncludes = array_filter($includes, function ($relation) use ($model) {
            if (!method_exists($model, $relation)) {
                return false;
            }

            $relationInstance = null;

            // Try to suppress errors if method requires parameters or other
            try {
                $relationInstance = $model->$relation();
            } catch (\Throwable $e) {
                return false;
            }

            return $relationInstance instanceof \Illuminate\Database\Eloquent\Relations\Relation;
        });

        $query->with($validIncludes);

        return $query;
    }

    /**
     * Dynamically pick fields from included relations
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return array
     */
    public function toArrayWithDynamicIncludes(Request $request): array
    {
        $data = $this->attributesToArray();
        $includes = array_filter(explode(',', $request->query('include', '')));

        foreach ($this->getRelations() as $relationName => $relatedModel) {
            if (!$relatedModel || !in_array($relationName, $includes)) {
                continue;
            }

            // Try to resolve the FK field using reflection on the relation method
            if (!method_exists($this, $relationName)) {
                continue;
            }

            $relation = $this->$relationName();

            if (!$relation instanceof \Illuminate\Database\Eloquent\Relations\BelongsTo) {
                continue; // support only belongsTo for now
            }

            $foreignKey = $relation->getForeignKeyName();

            // Replace the FK field with the related model array
            if (array_key_exists($foreignKey, $data)) {
                $data[$foreignKey] = $relatedModel->toArray();
            }

            // Optionally, also assign under relation name if different
            if (!isset($data[$relationName])) {
                $data[$relationName] = $relatedModel->toArray();
            }
        }

        return $data;
    }
}
