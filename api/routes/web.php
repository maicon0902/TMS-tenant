<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['message' => 'TMS API is running'];
});

