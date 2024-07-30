<?php

use App\Http\Controllers\HomePageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TarifController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('front.index');
});

Route::post('/get-contact',[HomePageController::class, 'getContact'])->name('contact.get');
Route::middleware('auth')->group(function () {
    Route::get('/admin',[HomePageController::class, 'index'])->name('admin');
   Route::resource('/admin/products',ProductController::class);
   Route::post('/admin/product/{product}/edit',[ProductController::class, 'update']);
   Route::resource('/admin/tarifs',TarifController::class);
});
require __DIR__.'/auth.php';
