<?php

namespace Websanova\EasyCache\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
	use \Websanova\EasyCache\EasyCache;

	public function scopeDomain($q)
	{
		$q->where('domain_id', config('app.domain')->id);
	}

    public function scopeActive($q)
    {
        return $q->where('status', 'active');
    }

    public function scopePending($q)
    {
        return $q->where('status', 'pending');
    }

    public function scopeInactive($q)
    {
        return $q->where('status', 'deleted');
    }
}
