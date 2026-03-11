<?php

use App\Http\Controllers\ReportsController;
use App\Http\Controllers\AppController as AppController;

Route::get('/reports', [ReportsController::class, 'index']);

Route::get('{all}', [AppController::class, 'index'])->where('all', '^((?!api).)*')->name('index');



