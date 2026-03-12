<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/user/getdatabyuuid/{uuid}', [UserController::class, 'getDataByUuid']);