<?php

use Websanova\EasyCache\Models\Item;

class EasyCacheTest extends TestCase
{
    public function testGetBy()
    {
    	\Cache::tags('item')->flush();

    	$model = Item::getById(1);
        $this->assertTrue($model->id === '1');
        $this->assertTrue( ! $model->isFromCache());

        $model = Item::getBySlug('one');
        $this->assertTrue($model->id === '1');
        $this->assertTrue($model->isFromCache());

        $model = Item::find(1);
        $this->assertTrue($model->id === '1');
        $this->assertTrue( ! $model->isFromCache());
    }

    public function testGetAll()
    {
        // Test mixed controller models fromCache
        // Length etc...
    }

    public function testPagination()
    {


    }

    public function testStatus()
    {
        
    }
}