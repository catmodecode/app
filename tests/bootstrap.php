<?php

use Illuminate\Support\Facades\Artisan;

file_put_contents(__DIR__ . '/../database/database.sqlite', '');

$argv = ['--env=testing'];

require __DIR__ . '/../bootstrap/app.php';

$app->boot();

Artisan::call('migrate', [
   '--database' => 'testing',
   '--seed' => true,
   '--force' => true
]);