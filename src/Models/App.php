<?php

namespace Websanova\EasyCache\Models;

class App extends BaseModel
{
    protected $table = '';

    public function activeItems()
    {
    	$this->hasMany('\Websanova\EasyCache\Models\Domain', 'websanova_easycache_domains');
    }
}
