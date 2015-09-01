<?php

namespace Websanova\EasyCache\Models;

class Domain extends BaseModel
{
    protected $table = 'websanova_easycache_domains';

    protected $cacheKey = 'domains';

    public $cacheBy = 'slug';

    public $timestamps = false;

    public function slug()
    {
        return $this->select('*');
    }

    public function items()
    {
    	return $this->hasMany('\Websanova\EasyCache\Models\Item');
    }

    public function comments()
    {
        return $this->hasMany('\Websanova\EasyCache\Models\Comment');
    }

    public function recountItems()
    {
        return $this->items()->active();
    }

    public function recountComments()
    {
        return $this->comments()->active();
    }
}
