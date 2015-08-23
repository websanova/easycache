<?php

namespace Websanova\EasyCache;

use Cache;

trait EasyCacheTrait
{
    protected $cached = false;

    protected $cacheKey = '';

    protected $cacheTime = 60;

    protected $cacheBy = 'id';

    public function newCollection(array $models = [])
    {
        return new EasyCacheCollection($models);
    }

    public function scopeCache($q, $val = null, $passthru = null)
    {
        if (is_int($val) || is_string($val)) {
            if ( ! is_null($passthru)) {
                $model = $this->{$passthru}()->select($this->cacheBy)->where($passthru, $val)->first();

                if (empty($model)) return null;

                $val = $model->{$this->cacheBy};
            }

            return $this->getFromCache($val);
        }

        $val = ! is_null($val) ? $val : ($this->perPage === false ? false : true);
        
        $perPage = is_int($passthru) ? $passthru : $this->perPage;

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

    protected function getFullCacheKey($val = null)
    {
        return $this->getCacheKey() . '-' . $this->cacheBy . '-' . ($val ?: $this->{$this->cacheBy});
    }

    public function inc($field, $factor = 1)
    {
        $model = $this->cached === true ? $this->getFromCache($this->{$this->cacheBy}) : $this;

        $model->{$field} += 1;

        if ($model->{$field} % $factor === 0) {
            $model->save();
        }

        if ($this->cached === true) {
            $model->recache();
        }
    }

    public function recount($field, $args = [])
    {
        $table = explode('_', $field);

        $this->{$field} = call_user_func_array([$this, $table[0]], $args)->count();
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





    /*protected $cacheKeySingular = '';

    protected $cacheKeyPlural = '';

    protected $cacheModels = true;

    protected $cacheTime = 60;

    protected $cacheBy = 'id';

    protected $cached = false;

    public function newCollection(array $models = [])
    {
        return new EasyCacheCollection($models);
    }

    public function scopeParams($q)
    {
        return $q->select('*');
    }

    public function scopeFilters($q)
    {
        return $q;
    }

    public function scopeWiths($q)
    {
        return $q;
    }

    public function scopeAppends($q)
    {
        return $q;
    }

    public function scopeOrders($q)
    {
        return $q->orderBy('created_at', 'desc');
    }

    public function scopeCache($cache = true)
    {
        $this->cacheModels = $cache;

        return $this;
    }

    public function scopeOrdersBuilder($q)
    {
        if (empty($q->getQuery()->orders)) {
            $q->orders();
        };

        return $q;
    }

    public function relationBuilder($q, $method)
    {
        return call_user_func_array([$q, lcfirst($method)], array_slice(func_get_args(), 2));
    }

    protected function getBy($field, $value)
    {
        if ($this->cacheModels === false) {
            return self::params()->filters()->withs()->appends()->where($field, $value)->first();
        }

        if ($field === $this->cacheBy) {
            $key = $value;
        }
        else {

            // TODO: Should we cache these ids as well?
            $model = $this->select($this->cacheBy)->where($field, $value)->first();

            if (empty($model)) return null;

            $key = $model->{$this->cacheBy};
        }

        return $this->getFromCache($key);
    }

    protected function getAll()
    {
        $q = call_user_func_array([$this, 'getRelationQuery'], func_get_args());

        return self::getFactory($q->get());
    }

    protected function getPag()
    {
        $q = call_user_func_array([$this, 'getRelationQuery'], func_get_args());

        return self::getFactory($q->paginate());
    }

    protected function getRelationQuery($method = '')
    {
        $q = $this;

        $cache = $q->cacheModels;

        if ( ! empty($method)) {
            $args = func_get_args();
            array_unshift($args, $q);
            $q = call_user_func_array([$q, 'relationBuilder'], $args);

            $cache = $q->getModel()->cacheModels;
        }

        if ($cache === true) {
            $q = $q->select('id')->filters()->appends();
        }
        else {
            $q = $q->params()->filters()->withs()->appends();
        }

        $q = $q->ordersBuilder();

        return $q;
    }

    protected function getFactory($models)
    {
        if ($models->isEmpty()) return $models;

        foreach ($models as $key => $model) {
            if ( ! $model->cacheModels) continue;

            $relations = $models[$key]->getRelations();

            $models[$key] = $model->getFromCache($model->id);

            foreach ($relations as $relation_key => $relation_value) {
                $models[$key]->setRelation($relation_key, $relation_value);
            }
        }

        //TODO: Check this is still a collection.

        return $models;
    }

    protected function getFromCache($value)
    {
        $key = $this->getKeySingular();
        $cache_key = $this->getCacheKey($value);

        if (Cache::tags($key)->has($cache_key)) {
            $model = Cache::tags($key)->get($cache_key);
            $model->cached = true;
        }
        else {
            $model = self::params()->filters()->withs()->where($this->cacheBy, $value)->first();

            if ($model instanceof self) {
                Cache::tags($key)->put($cache_key, $model, $this->cacheTime);
            }
        }

        return $model;
    }

    public function getKeySingular()
    {
    	if ( ! empty($this->keySingular)) {
    		return $this->keySingular;
    	}

    	return strtolower(class_basename($this));
    }

    public function getKeyPlural()
    {
    	if ( ! empty($this->keyPlural)) {
    		return $this->keyPlural;
    	}

        return $this->getKeySingular() . 's';
    }

    public function getCacheKey($value)
    {
        return $this->getKeySingular() . '-' . $this->cacheBy . '-' . $value;
    }

    public function isFromCache()
    {
        return $this->cached;
    }

    /**
     * Checks id's in current relationship with
     * that of an input of the same name.
     * 
     * @param  string  $relation 
     * @param  string  $ids
     * @return boolean
     *
    public function isDirtyRelation($relation = '')
    {
        if (\Input::get($relation) && isset($this->{$relation})) {
            $current = $this->{$relation}->lists('id')->all();
            $new = explode(',', \Input::get($relation));

            $diff = count(array_diff($tags_current, $tags_new)) + count(array_diff($tags_new, $tags_current));

            return $diff > 0;
        }

        return false;
    }

    protected function getIdsInList($values)
    {
        $q = $this->whereIn($this->cacheBy, $values);

        if ($this->cacheModels !== true) {
            $q->params()->withs()->appends();
        }

        $models = $q->get();

        return $this->getFactory($models);
    }

    public function inc($field, $factor = 1)
    {
        $model = $this->cacheModels === true ? $this->getFromCache($this->{$this->cacheBy}) : $this;

        $model->{$field} += 1;

        if ($model->{$field} % $factor === 0) {
            $model->save();
        }

        if ($this->cacheModels === true) {
            $model->recache();
        }
    }

    public function recount($field, $args = [])
    {
        $table = explode('_', $field);

        $this->{$field} = call_user_func_array([$this, $table[0]], $args)->filters()->count();
        $this->save();

        return $this;
    }

    public function recache()
    {
        $key = $this->getKeySingular();
        $cache_key = $this->getCacheKey($this->{$this->cacheBy});

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
        $key = $this->getKeySingular();
        $cache_key = $this->getCacheKey($this->{$this->cacheBy});

        Cache::tags($key)->forget($cache_key);

        return $this;
    }

    public function __call($name, $args)
    {
        if (method_exists($this, $name)) {
            return call_user_func_array([$this, $name], $args);
        }
        elseif (substr($name, 0, 5) === 'getBy') {
            array_unshift($args, strtolower(substr($name, 5)));
            return call_user_func_array([$this, 'getBy'], $args);
        }
        elseif (substr($name, 0, 6) === 'getAll') {
            array_unshift($args, substr($name, 6));
            return call_user_func_array([$this, 'getAll'], $args);
        }
        elseif (substr($name, 0, 6) === 'getPag') {
            array_unshift($args, substr($name, 6));
            return call_user_func_array([$this, 'getPag'], $args);
        }

        return parent::__call($name, $args);
    }*/
}