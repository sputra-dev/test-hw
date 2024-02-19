<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/looping', function () {
    $output = '';
    for ($i = 1; $i <= 100; $i++) {
        if ($i % 3 == 0 && $i % 5 == 0) {
            $output .= "TigaLima<br>";
        } else if ($i % 3 == 0) {
            $output .= "Tiga<br>";
        } else if ($i % 5 == 0) {
            $output .= "Lima<br>";
        } else {
            $output .= $i . "<br>";
        }
    }

    return view('looping', ['output' => $output]);
});
