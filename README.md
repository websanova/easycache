# EasyCache

A simple on demand caching extension for Laravel.

Works similar to `paginate()` or `get()` extending to also allow `cache()`.

## Install

~~~
composer require websanova/easycache
~~~

## Examples

### Models:

~~~
$item = Item::cache($id);            // Get via integer.
$item = Item::cache($val);           // Get via string value.
$item = Item::cache($email, 'email') // Get from cache via email `$passthrough`.
~~~

### Collections:

~~~    
$items = Item::cache();         // Paginated, using `$perPage`.
$items = Item::cache(true, 40); // Paginated with custom.
$items = Item::cache(false);    // No pagination (`get`).
~~~

### Queries:

We can also build the queries as we would normally.

~~~
$items = Item::active()->user()->cache();

$item = Item::cache($id);
$comments = $item->comments()->active()->cache();
~~~

Note the methods `active` and `user`, `comments` are all arbitrary.

### Flush, Reset, Recount:

We also have some helper methods that work both on models and collections.

~~~
$item->flush();                      // Flush item.
$items->flush();                     // Flush all items.
$item->comments()->cache()->flush(); // Flush all comments.

$item->recount('comments_total');    // Reset `comments_total`.

$items->reset();                     // Flush and re-cache items.
~~~

## Setup

### Trait:

The extension comes as a trait so just include it on any model you want to use it.

~~~
class Item extends \Illuminate\Database\Eloquent\Model
{
    use \Websanova\EasyCache\EasyCache;

    ...
}
~~~

Usually it's best to just create a `BaseModel` and include it once there.

### Caching:

You may be wondering at this point how do I control what is cached. This is controlled by a property called `cacheBy` set to `id` by default.

Based on this value the `cache()` method will call a function (or scope) by this name. In this method you specify what actually gets cached.

~~~
class Item extends \Illuminate\Database\Eloquent\Model
{
    use \Websanova\EasyCache\EasyCache;

    public function id()
    {
        return $this->select('id', 'slug', 'name')
                    ->active()
                    ->with('user');
    }

    ...
}
~~~

I like to use a plain `id()` method. But it's also useful to set it as `scopeId` for re-use when not using `cache`.

Note because the `id()` method actually controls the data we don't need to specify any `select` parameters in our query builders. The cache will work off the `cacheBy` field only.

## Methods

### `cache() passthrough`

Sometimes we will want to get an item from cache which is stored by `id` but through a different field. A good example is getting a user by an `email` or `facebook_id`. We don't want to cache the same data twice. This will do a quick look up first using the `passthrough` field before fetching the model by `id`.

~~~
User::cache('rob@websanova.com', 'email');

---

public function id()
{
    ...
}

public function email()
{
    return $this;
}
~~~

The passthrough field will try to find a method by the same name where you can specify additional parameters for the look up. If you don't have any just `return $this`.

### `flush()`

This works just as the `Cache::flush()` method. It will just remove the item from the cache. It also works on collections.

~~~
$item = Item::cache($id);

$item->flush();
$item->comments()->cache()->flush();
~~~

### `reset()`

Flushes item(s) from cache and calls them again. This performs a full reset of the model(s) in the cache.

~~~
$item = Item::cache($id);

$item->reset();
$item->comments()->cache()->reset();
~~~

### `recount()`

Actually this one has nothing to do with caching but it's a common feature I use along with `flush` and `reset`.

It performs a full recount rather than an increment or decrement. This is for more accurate counting as `total` counts can be off easily when errors occur. Perhaps not needed on each request but also useful in a command to reset totals.

~~~
$item = Item::cache($id);

$item->recount('comments_total');                    // Will use `comments()` relation.
$item->recount('comments_total', 'commentsRecount'); // Specify relation to use for count.
~~~

### `recache()`

This just overwrites an item in the cache. Useful for increments and decrements. Note this is not available with collections.

~~~
$item = Item::cache($id);

$item->comments_total += 1;
$item->recache();
~~~

### `cacheInc()`

This works similar to the Laravel `increment()` method. However in this case we increment the value in the cache and only perform a save operation.

~~~
$item = Item::cache($id);

$item->cacheInc('comments_total');
~~~

We can also increment by a number and also specify a factor by which to perform the actual `save`.

~~~
$item->cacheInc('comments_total', 1, 10);
~~~

In this case we would do the `save` only once every ten times.

### `cacheDec()`

Calls the `cacheInc()` method but as a negative value.

~~~
$item = Item::cache($id);

$item->cacheDec('comments_total');
~~~

Note that you should supply a positive number and `cacheDec` will pass it as a negative for you.

### `cacheOrFail()`

Similar to `findOrFail()` except that it works in conjunction with the cached items. Small difference is that it only works for models and not collections (for now).

~~~
Item::cacheOrFail($id);
~~~

It also throws the same `ModelNotFoundException` as `findOrFail` if a model is not found.

Note that if the model is not cached, it will cache it first then return it.

There is also a second parameter for a set or arguments it can receive. This is in case you want to keep a more loose set of models in your cache. For instance without status, so that all "active", "pending", etc items get cached. This allows you to set additional parameters which are useful when route model binding for one. You can set additional parameters for strictness there.

~~~
Item::cacheOrFail($id, [
    'status' => 'active'
    ...
]);
~~~

## Parameters

### `cached`

This is mostly used for testing but is convenient to look up to know whether the model is actually from the cache or not.

### `cacheKey`

This field is set by default using the `getTable()` method. However you can overwrite it in case you have some more complicated class structure in your models.

It will cache the `items` using this key like so:

~~~
Cache::tags('items')->put('items-id-14', $model, $this->cacheTime);
Cache::tags('items')->put('items-slug-en', $model, $this->cacheTime);
~~~

### `cacheTime`

The length of time the items stays in the cache. It's best to set this in a `BaseModel` with appropriate overwrites.

### `cacheBy`

The field which we will cache by set to `id` by default.

## Appending

In some cases we will to have some additional data come along with our query which is not stored in the cache.

We want to be able to take advantage of the way `Eloquent` automatically builds our relationships when using `with`. This is useful in a case such as a users score on some item. We can store the items in cache but not the user score (at least not along with the item).

We can simply include this in our query and the `cache` method will automatically build it for us.

~~~
$items = Item::active()->orderBy('created_at')->with('score')->cache();
~~~

This will perform just one query for getting the `score` rather than multiple queries if we didn't include it in the query beforehand.

## License

Laravel Easy Cache is licensed under [The MIT License (MIT)](https://github.com/websanova/easycache/blob/HEAD/LICENSE).
