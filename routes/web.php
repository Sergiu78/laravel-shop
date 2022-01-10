<?php

use TCG\Voyager\Voyager;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PetController;

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

Route::get('/', 'PetController@index')->name('home');
Route::get('/about', 'PetController@about')->name('about');
Route::get('/shop-page', 'PetController@shopPage')->name('shop-page');
Route::get('/login-register', 'PetController@loginRegister')->name('login-register');
Route::get('/my-account', 'PetController@myAccount')->middleware('auth')->name('my-account');
Route::get('/checkout', 'PetController@checkout')->middleware('auth')->name('checkout');
Route::post('/make-order', 'PetController@makeOrder')->name('make-order');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/add-cart', 'PetController@addCart')->name('add-cart');

require __DIR__.'/auth.php';


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});
