<?php

use James\Env\Http\Controllers\EnvController;

Route::get('env/{id}/delete', EnvController::class.'@delete');
Route::post('env/delete-all', EnvController::class.'@deleteAll');
Route::resource('env', EnvController::class);