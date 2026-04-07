<?php

use App\Http\Controllers\ReportsController;
use App\Http\Controllers\AppController as AppController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/reports', [ReportsController::class, 'index']);

Route::get('/storage/{path}', static function (string $path) {
	if (!Storage::disk('public')->exists($path)) {
		abort(404);
	}

	return response()->file(storage_path('app/public/'.$path));
})->where('path', '.*')->name('storage.fallback');

Route::get('{all}', [AppController::class, 'index'])->where('all', '^((?!api).)*')->name('index');



