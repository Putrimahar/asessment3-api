<?php

use App\Http\Controllers\MakananController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/makanan', [MakananController::class, 'index'])->name('show');
Route::post('/makanan/store', [MakananController::class, 'store'])->name('store');

Route::POST('/makanan/edit/{id}', [MakananController::class, 'update'])->name('update');

Route::delete('/makanan/delete/{id}', [MakananController::class, 'destroy'])->name('destroy');
