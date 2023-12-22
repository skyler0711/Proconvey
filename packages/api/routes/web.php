<?php

use App\Http\Controllers\Webhooks\MobileWebhookController;
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

Route::stripeWebhooks('/webhooks/stripe');
Route::webhooks('/webhooks/yotisign', 'yotisign');
Route::webhooks('/webhooks/yotiidv', 'yotiidv');
Route::get('/webhooks/mobile', MobileWebhookController::class);
