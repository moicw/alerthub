<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('flow');
});

Route::get('/projects', fn () => view('projects'));
Route::get('/subscribers', fn () => view('subscribers'));
Route::get('/alert-rules', fn () => view('alert-rules'));
Route::get('/webhook-sources', fn () => view('webhook-sources'));
Route::get('/notifications', fn () => view('notifications'));
Route::get('/webhooks', fn () => view('webhooks'));
Route::get('/flow', fn () => view('flow'));
