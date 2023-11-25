<?php

use App\Services\Holidays\Handler;
use DefStudio\Telegraph\Facades\Telegraph;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use DefStudio\Telegraph\Models\TelegraphBot;
use DefStudio\Telegraph\Models\TelegraphChat;
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

Route::get('', function () {
    /**
     * @var TelegraphBot $bot
     * @var TelegraphChat $chat
     */
    $bot = TelegraphBot::query()->find(1);

    $bot->registerCommands([
        'today' => 'Праздники сегодня',
    ])->send();
});
