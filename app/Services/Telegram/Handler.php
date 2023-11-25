<?php

namespace App\Services\Telegram;

use DefStudio\Telegraph\Facades\Telegraph;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use DefStudio\Telegraph\Keyboard\ReplyButton;
use DefStudio\Telegraph\Keyboard\ReplyKeyboard;
use Illuminate\Support\Facades\Log;

class Handler extends WebhookHandler
{

    private array $holidays;

    public function __construct()
    {
        parent::__construct();
        $this->holidays = \App\Services\Holidays\Handler::getHolidays();
    }

    public function start() : void
    {
        $this->bot->registerCommands([
            'today' => 'Праздники сегодня',
        ])->send();
    }

    public function info() : void
    {
        $index = $this->data->get('index');

        Log::debug('hilday info', $this->holidays['data']['holidays'][$index]);

        $expiredDate = now()->addMinutes(2);

        $this->chat
            ->message($this->holidays['data']['holidays'][$index]['text'])
            ->expire($expiredDate)
            ->send();
    }

    public function today() : void
    {
        if($this->holidays['success']) {

            $buttons = collect($this->holidays['data']['holidays'])->map(function($holiday, $key) {
                return Button::make($holiday['name'])->action('info')->param('index', $key);
            })->toArray();

            $this->chat->message('Праздники сегодня')->keyboard(
                Keyboard::make()->buttons($buttons)
            )->send();

        } else {
            $this->chat->reply($this->holidays['data']['message']);
        }
    }
}
