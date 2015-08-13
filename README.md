


## Helper Methods

There are some things that are quite common so they are included automatically.

`order`


## Testing

If you are testing the package you will need some migrations.

~~~
./artisan migrate --path=vendor/websanova/easy-cache/migrations
~~~

## Relationships

--discuss two levels of relationships - the getall relationship calls should require minimal calls.
--for tables you are using include with (you should specify more details in the data, as these will be part of the cached object)
    --note you should always keep minimal information here only, specifically what you need).


**It's a good idea to define explicit relationships and call those.

## Appending

If you need to append some data to cached items after they are pulled from cache you will want to take advantage of the `with` method that comes with Eloquent.

To do this just specify whatever you need in your relationship (->with('lessons')) these will be appended automatically and will only execute one query rather than multiple queries.

Also note that if you have some consistent data, like a list of tags for an item, you should use the `cacheWith` and keep that data with each item in the cache.

## Setup

The best way to use this package (trait) is to create a local `BaseModel` and include the trait there.

From there you can add any additional scopes specific to your app. You can also override any of the traits method such as the `active`, `inactive` and `deleted`.

There is also a `common` method you can extend. Which is empty but always you to include any common elements in one area.

## Pagination

Pagination uses the built in `perPage` parameter. You can use this as normally by setting your own default in a base model or within a model.

## Expiration

The cache expiration is by default set to `60 minutes`. You can set your own default in a base model. You can also set it individually per Model.

```
    protected $cacheTime = 90; // In minutes.
```

## `order()`

When using order you can set a default in your base model or within the model itself.

However note that if you use `orderBy()` on a relationship that you call using `getAll()` then `order()` function will no longer be applied.

This allows you to fully customize relationships if need be. If you still need the default just call `->order()->orderBy($column, $direction)`.

