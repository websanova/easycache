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
}
