<?php

declare(strict_types=1);

use App\Http\Controllers\RequestProfitController;
use Illuminate\Support\Facades\Route;

Route::post('/crawler', RequestProfitController::class);
