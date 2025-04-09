<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // temporary redirect until the example page its finish
    return redirect()->to('/admin');
});
