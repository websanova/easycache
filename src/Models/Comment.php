<?php

namespace Websanova\EasyCache\Models;

class Comment extends BaseModel
{
    protected $table = 'websanova_easycache_comments';

    protected $cacheKey = 'comments';

    public function id()
    {
    	return $this->select('*');
    }
}