<?php

Route::get('easy-cache/test', function () {

	\Config::set('app.domain', \Websanova\EasyCache\Models\Domain::find(1));

	//dd(\Websanova\EasyCache\Models\Item::getAll());
	$items = config('app.domain')->getAllActiveItems();

	$items->flush();

	return 'Test';
});