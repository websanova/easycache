# EasyCache

On demand caching library. This is a simple trait you can use to extend your models. It provides an easy to use caching mechanism with simple syntax and a lot of flexibility.

There is only one function you will need to call named `cache`. It's used for retrieving a model as well as a collection of models.

## Basic Usage

~~~
$item = Item::cache(4);                   // Model by id
$item = Item::cache('some-item', 'slug'); // Model by id via slug

$items = Item::cache();                   // Items with no filtering
$items = Item::every()->cache();          // Items with `every` filter.
$items = Item::every('status')->cache();  // Items with `every` filter and status.

$item->comments()->cache();               // Item comments (filter via relation)
$item->comments('active')->cache();       // Item comments (filter via relation and status)
~~~

The functions are completely up to the developer to design. This allows for maximum flexibility to setup the relationships.

Note that the `every` function above is just a made up `scope` name that developers would define themselves.

## Cache Setup

Whether you retrieve a model or a collection we will have to know what to store in the cache in the first place.

By default there is a `cacheBy` parameter set to `id` in the trait. This parameter will be used for caching. You can of course overwrite it in a base model or in each individual model.

You will then need to set a corresponding function that the cache mechanism will use to know what to store in the model.

~~~
class Item extends Eloquent {
    
    use \Websanova\EasyCache\EasyCacheTrait;

    protected $cacheBy = 'id'; // optional

    public function id()
    {
        return $this->params()->filters()->status()->with('user');
    }

    ...
}
~~~

This is what each individual model will store for the cached item. If you change the `cacheBy` parameter to `slug` then you would need to name the function `slug()`.

## Models

As long as you provide a valid `integer` or `string` the `cache` method will try to return a single model.

~~~
Item::cache(5); // Pulls a single model.
~~~

We can also create a `passthru` function in case we need to pull a model by some other field like a `slug`. Note that this `passthru` will not be cached. It's only a way to get to the item in the cache.

~~~
Item::cache('some-item', 'slug');
~~~

You will then need to provide a `slug()` `passthru` method for any necessary filtering. 

~~~
class Item extends Eloquent {
    
    use \Websanova\EasyCache\EasyCacheTrait;

    protected $cacheBy = 'id'; // optional

    public function id()
    {
        return $this->params()->filters()->status()->with('user');
    }

    public function slug()
    {
        return $this->filters();
    }

    ...
}
~~~

In the `passthru` method we just need to provide filtering. Any `select` parameters will be ignored. Likewise in the `id` caching method setting any parameters will be ignore. We add it simply in case we want to call the method directly.

## Collections

To get a collection of models from the cache we can just call `cache`. It accepts one boolean argument for pagination. By default it is set to `true`.

For getting a collection we can set a method to define any `filters`, `ordering` or `with` functions.

~~~
class Item extends Eloquent {
    
    use \Websanova\EasyCache\EasyCacheTrait;

    public function comments($status = 'active')
    {
        return $this->hasMany('Comment')->params()->filters()->{$status}()->orders();
    }

    public function scopeEvery($q, $status = 'active')
    {
        return $q->params()->filters()->{$status}()->orders();
    }
}
~~~

To get all items.

~~~
Item::every()->cache();
~~~

Get an items comments.

~~~
$item->comments()->cache();
~~~

## Base Model

With the use of a base model a lot of the relations can be simplified. A sample might look like this.

~~~
class Model extends Eloquent
{
    use \Websanova\EasyCache\EasyCacheTrait;

    public function id()
    {
        return $this->params()->filters()->withs();
    }

    public function slug()
    {
        return $this->filters();
    }

    public function scopeEvery($q, $status = 'active')
    {
        return $q->params()->filters()->{$status}()->orders();
    }
}

The above are just generic builders we can work off. We would then only need to define our custom scope methods `params`, `filters`, `withs`, `orders` and any `status` fields.

We can then call these methods and add to them.

~~~
class Item extends Model {

    public function comments($status = 'active')
    {
        return $this->hasMany('Comment')->every($status)->additionalFilters();
    }

    public function scopeEvery($q, $status = 'active')
    {
        return parent::scopeEvery($q, $status)->additonalFilters();
    }
}
~~~

You can pretty much build these up anyway that pleases you.

## Advanced Usage

A few more parameters to be aware of are `cacheKey`, `cacheTime` and `cached`.

The `cacheKey` is just the tables name obtained using the `getTable()` method. If you need to specify another key in case you have some kind of parent class you can set this parameter to use as the key for caching.

The `cacheTime` is just the time the item will remain in the cache before expiring.

The `cached` parameter is used mainly for testing to determine where the model was obtained from.

## Pagination

If you need to you can disable pagination for a single query you can call `->cache(false)`.

If you also want to modify the `perPage` number you can supply a second argument `->cache(true, 4)`.

If you have a model that will never use pagination you can set the `perPage` property to `false`.

## Collections

Adding the `EasyCacheTrait` means we'll also be adding our own `EasyCacheCollection` class which Laravel will extend from.

This class provides some additional short hand features for common tasks on cached models. This includes the methods described below which are available both to the Model and Collection.

## `flush()`

Flushes the models out of the cache (does not re-cache).

~~~

~~~

## `reset()`

Recalls the model and overwrites the existing in cache.

~~~

~~~

## `recache()`

Takes a model and overwrites the cache.

~~~

~~~

## `recount()`

 Recounts a total based on `field` to `relation` name matching.

~~~

~~~

## `inc()`

Shorthand feature to reset an incrementing value in the cache without having to retrieve it again.

~~~

~~~

## RouteServiceProvider