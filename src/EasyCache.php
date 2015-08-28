<?php

namespace Websanova\EasyCache;

use Cache;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait EasyCache
{
    public $cached = false;

    protected $cacheKey = '';

    protected $cacheTime = 60;

    protected $cacheBy = 'id';

    public function newCollection(array $models = [])
    {
        return new EasyCacheCollection($models);
    }

    public function scopeCache($q, $val = null, $passthrough = null)
    {
        if (is_int($val) || is_string($val)) {
            if ( ! is_null($passthrough)) {
                $model = $this->{$passthrough}()->select($this->cacheBy)->where($passthrough, $val)->first();

                if (empty($model)) return null;

                $val = $model->{$this->cacheBy};
            }

            return $this->getFromCache($val);
        }

        $val = ! is_null($val) ? $val : ($this->perPage === false ? false : true);
        
        $perPage = is_int($passthrough) ? $passthrough : $this->perPage;

        $q->select($this->cacheBy);

        return $this->getFromCacheFactory($val === true ? $q->paginate($perPage) : $q->get());
    }

    protected function getFromCache($val = null)
    {
        $key = $this->getCacheKey();
        $cache_key = $this->getFullCacheKey($val);

        if (Cache::tags($key)->has($cache_key)) {
            $model = Cache::tags($key)->get($cache_key);
        }
        else {
            $model = $this->{$this->cacheBy}()->where($this->cacheBy, $val)->first();

            if ($model instanceof self) {
                $model->cached = true;
                Cache::tags($key)->put($cache_key, $model, $this->cacheTime);
            }
        }

        return $model;
    }

    protected function getFromCacheFactory($models)
    {
        if ($models->isEmpty()) return $models;

        foreach ($models as $key => $model) {
            $relations = $models[$key]->getRelations();

            $models[$key] = $model->getFromCache($model->{$this->cacheBy});

            foreach ($relations as $relation_key => $relation_value) {
                $models[$key]->setRelation($relation_key, $relation_value);
            }
        }

        return $models;
    }

    public function getCacheKey()
    {
        return ! empty($this->cacheKey) ? $this->cacheKey : $this->getTable();
    }

    public function getFullCacheKey($val = null)
    {
        return $this->getCacheKey() . '-' . $this->cacheBy . '-' . ($val ?: $this->{$this->cacheBy});
    }

    public function cacheInc($field, $amount = 1, $factor = 1)
    {
        $model = $this->cached === true ? $this->getFromCache($this->{$this->cacheBy}) : $this;

        $model->{$field} += $amount;

        if ($model->{$field} % $factor === 0) {
            $timestamps = $model->timestamps;
            
            $model->timestamps = false;
            $model->save(['timestamps' => false]); // Bug in Laravel using arg, for now we do it manually.
            $model->timestamps = $timestamps;
        }

        if ($this->cached === true) {
            $model->recache();
        }

        return $model;
    }

    public function cacheDec($field, $amount = 1, $factor = 1)
    {
        return $this->cacheInc($field, $amount * -1, $factor);
    }

    public function recount($field, $relation = null)
    {
        if (is_null($relation)) {
            $table = explode('_', $field);
            $relation = $table[0];
        }

        $this->{$field} = (string)call_user_func_array([$this, $relation], [])->count();
        $this->save();

        return $this;
    }

    public function recache()
    {
        $key = $this->getCacheKey();
        $cache_key = $this->getFullCacheKey();

        Cache::tags($key)->put($cache_key, $this, $this->cacheTime);

        return $this;
    }

    public function reset()
    {
        $this->flush();
        $this->getFromCache($this->{$this->cacheBy});

        return $this;
    }

    public function flush()
    {
        $key = $this->getCacheKey();
        $cache_key = $this->getFullCacheKey();

        Cache::tags($key)->forget($cache_key);

        return $this;
    }

    protected function cacheOrFail($id)
    {
        if (! is_null($model = $this->getFromCache($id))) {
            return $model;
        }

        throw (new ModelNotFoundException)->setModel(get_class($this));
    }
}
