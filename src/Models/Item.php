<?php

namespace Websanova\EasyCache\Models;

class Item extends BaseModel
{
    protected $table = 'websanova_easycache_items';

    protected $cacheKey = 'items';

    public function id()
    {
    	return $this->select('*');
    }

    public function slug()
    {
    	return $this;
    }

    public function comments()
    {
    	return $this->hasMany('\Websanova\EasyCache\Models\Comment');
    }

    public function recountComments()
    {
        return $this->comments()->active();
    }
}
