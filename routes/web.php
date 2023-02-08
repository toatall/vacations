<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\ProfileController;
use App\Models\PeriodsSoap;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    // return view('welcome');
    return redirect('/dashboard');
});

Route::get('schedule', [Controller::class, 'schedule'])
    ->middleware(['auth', 'verified'])->name('schedule');

Route::get('dashboard', [Controller::class, 'dashboard'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::get('dashboard', [Controller::class, 'dashboard'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::get('set-year', [Controller::class, 'setYear'])
    ->middleware(['auth', 'verified'])->name('set-year');

// Route::get('dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');    
});

require __DIR__.'/auth.php';
