<?php

use Websanova\EasyCache\Models\Item;
use Websanova\EasyCache\Models\Domain;
use Websanova\EasyCache\Models\Comment;

class EasyCacheTest extends TestCase
{
    public function __construct()
    {
        
    }

    public function testGetItemById()
    {
        \Cache::tags('items')->flush();

    	$item = Item::cache(1);
        $this->assertTrue($item->id === '1');
        $this->assertTrue($item->cached);

        $item = Item::find(1);
        $this->assertTrue($item->id === '1');
        $this->assertTrue( ! $item->cached);
    }

    public function testGetItemBySlugPassthrough()
    {
        \Cache::tags('items')->flush();

        $item = Item::cache('two', 'slug');
        $this->assertTrue($item->id === '2');
        $this->assertTrue($item->cached);
    }

    public function testGetDomainBySlug()
    {
        \Cache::tags('domains')->flush();

        $domain = Domain::cache('en');
        $this->assertTrue($domain->id === '1');
        $this->assertTrue($domain->cached);
    }

    public function testGetItems()
    {
        \Cache::tags('items')->flush();

        $items = Item::active()->cache();
        $this->assertTrue($items->count() === 3);

        $items = Item::inactive()->get();
        $this->assertTrue($items->count() === 2);

        $item = Item::cache(1);
        $comments = $item->comments()->active()->cache();
        $this->assertTrue($comments->count() === 2);
    }

    public function testGetItemsCustomPagination()
    {
        \Cache::tags('items')->flush();

        $items = Item::active()->cache(true, 1);
        $this->assertTrue($items->count() === 1);
    }

    public function testRecountDomainCountsReset()
    {
        \Cache::tags('domains')->flush();

        // Recount items in domain
        $domain = Domain::cache('en');

        $domain->recount('items_total', 'recountItems');

        // But in cache have old value.
        $domain = Domain::cache('en');
        $this->assertTrue($domain->items_total === '0');

        $domain->reset();
        $domain = Domain::cache('en');
        $this->assertTrue($domain->items_total === '2');
    }

    public function testRecountDomainCountsRecache()
    {
        \Cache::tags('domains')->flush();

        // Recount items in domain
        $domain = Domain::cache('en');

        $domain->recount('comments_total', 'recountComments');
        $this->assertTrue($domain->comments_total === '3');

        // But in cache have old value.
        $domain_in_cache = Domain::cache('en');
        $this->assertTrue($domain_in_cache->comments_total === '0');

        $domain->recache(); // No select query here.
        $domain = Domain::cache('en');
        $this->assertTrue($domain->comments_total === '3');
    }
}