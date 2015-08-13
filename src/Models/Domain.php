<?php

namespace Websanova\EasyCache\Models;

class Domain extends BaseModel
{
    protected $table = 'websanova_easycache_domains';

    public function activeItems()
    {
    	return $this->hasMany('\Websanova\EasyCache\Models\Item')->active()->order();
    }

    public function pendingItems()
    {
    	return $this->hasMany('\Websanova\EasyCache\Models\Item')->pending()->order();
    }

    public function deletedItems()
    {
    	return $this->hasMany('\Websanova\EasyCache\Models\Item')->deleted()->order();
    }

    public function scopeOrder($q)
    {
    	return $q->orderBy('id', 'asc');
    }

    //protected $perPage = 20;

    //protected $with = [];
}
