<?php

namespace App\Models\Utils;

use Illuminate\Database\Eloquent\Model;

trait CanRate
{
    public function ratings ($model = null)
    {
        $modelClass = $model ? $model : $this->getMorphClass();

        $morphoToMany = $this->morphToMany(
            $modelClass,
            'qualifier',
            'ratings',
            'qualifier_id',
            'rateable_id'
        );

        $morphoToMany
            ->as('rating')
            ->withTimestamps()
            ->withPivot('score', 'rateable_type')
            ->wherePivot('rateable_type', $modelClass)
            ->wherePivot('qualifier_type', $this->getMorphClass())
            ;

        return $morphoToMany;
    }

    public function rate(Model $model, float $score): bool
    {
        if($this->hasRated($model)){
            return false;
        }

        $this->ratings($model)->attach($model->getKey(),[
           'score' => $score,
            'rateable_type' => get_class($model)
        ]);
        return true;
    }

    public function unrate(Model $model): bool
    {
        if (! $this->hasRated($model)) {
            return false;
        }

        $this->ratings($model->getMorphClass())->detach($model->getKey());

        return true;
    }

    public function hasRated(Model $model): bool
    {
       return ! is_null($this->ratings($model->getMorphClass())->find($model->getKey()));
    }
}
