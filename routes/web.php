<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function ()  {
    return view('landing', ['apiVersion'=> API_VERSION]);
});

Route::get('/api/{API_VERSION}/doc', function () {
    return view('doc', ['apiVersion'=> API_VERSION]);
});
