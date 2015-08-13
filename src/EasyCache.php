<?php

namespace Websanova\EasyCache;

use Cache;

trait EasyCache
{
    protected $keySingular = '';

    protected $keyPlural = '';

    protected $cacheModels = true;

    protected $fromCache = false;

    protected $cacheTime = 60;

    public function newCollection(array $models = [])
    {
        return new EasyCacheCollection($models);
    }

    public function scopeCacheBuilder($q)
    {
        return $q;
    }

    public function scopeCacheWith($q)
    {
        return $q;
    }

    public function relationBuilder($q, $relation)
    {
        return call_user_func_array([$q, lcfirst($relation)], array_slice(func_get_args(), 2));
    }

    protected function getBy($field, $value)
    {
        if ($this->cacheModels === false) {
            return self::cacheBuilder('single')->cacheWith('single')->where($field, $value)->first();
        }

        if ($field === 'id') {
            $id = $value;
        }
        else {

            // TODO: Should we cache these ids as well?
            $model = $this->cacheBuilder('single')->select('id')->where($field, $value)->first();

            if (empty($model)) return null;

            $id = $model->id;
        }

        return $this->getFromCache($id);
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

        if ( ! empty($method)) {
            $args = func_get_args();
            array_unshift($args, $q);

            $q = call_user_func_array([$q, 'relationBuilder'], $args);
        }

        $q->cacheBuilder('multi');

        if ($this->cacheModels === true) {
            $q = $q->select('id');
        }
        else {
            $q = $q->cacheWith('multi');
        }

        return $q;
    }

    protected function getFactory($models)
    {
        if ($this->cacheModels === true) {
            foreach ($models as $key => $model) {
                $relations = $models[$key]->getRelations();

                $models[$key] = $model->getFromCache($model->id);

                foreach ($relations as $relation_key => $relation_value) {
                    $models[$key]->setRelation($relation_key, $relation_value);
                }
            }
        }

        //TODO: Check this is still a collection.

        return $models;
    }

    protected function getFromCache($id)
    {
        $key = $this->getKeySingular();
        $cache_key = $this->getKeySingular() . '-id-' . $id;

        if (Cache::tags($key)->has($cache_key)) {
            $model = Cache::tags($key)->get($cache_key);
            $model->fromCache = true;
        }
        else {
            $model = self::cacheBuilder('single')->cacheWith('single')->find($id);

            if ($model instanceof self) {
                Cache::tags($key)->put($cache_key, $model, $this->cacheTime);
            }
        }

        return $model;
    }

    protected function getIdsInList($ids)
    {
        $q = $this->whereIn('id', $ids);

        if ($this->cacheModels !== true) {
            $q->cacheWith('multi');
        }

        $models = $q->get();

        return $this->getFactory($models);
    }

    public function getKeySingular()
    {
    	if ( ! empty($this->singular)) {
    		return $this->singluar;
    	}

    	return strtolower(class_basename($this));
    }

    public function getKeyPlural()
    {
    	if ( ! empty($this->plural)) {
    		return $this->plural;
    	}

        return $this->getKeySingular() . 's';
    }

    public function isFromCache()
    {
        return $this->fromCache;
    }

    public function getUriAttribute()
    {
        return '/' . $this->getKeyPlural() . '/' . $this->id . ( ! empty($this->slug) ? '/' . $this->slug : '');
    }

    public function recount()
    {
        // recount some field

        return "SELECT COUNT(*) FROM lessons WHERE lessons.{$this->key}_id = {$this->table}.id AND lessons.status = 'active'";
    }

    public function reset()
    {
        // Flush and reset the model.
    }

    public function flush($ids = null)
    {
        $key = $this->getKeySingular();

        Cache::tags($key)->forget($key . '-' . $this->id);

        return $this;
    }

    public function __call($name, $args)
    {
        if (method_exists($this, $name)) {
            return call_user_func_array([$this, $name], $args);
        }
        elseif (substr($name, 0, 5) === 'getBy' && ! empty($method = strtolower(substr($name, 5)))) {
            array_unshift($args, strtolower($method));
            return call_user_func_array([$this, 'getBy'], $args);
        }
        elseif (substr($name, 0, 6) === 'getAll' && ! empty($method = substr($name, 6))) {
            array_unshift($args, $method);
            return call_user_func_array([$this, 'getAll'], $args);
        }
        elseif (substr($name, 0, 6) === 'getPag' && ! empty($method = substr($name, 6))) {
            array_unshift($args, $method);
            return call_user_func_array([$this, 'getPag'], $args);
        }

        return parent::__call($name, $args);
    }
}