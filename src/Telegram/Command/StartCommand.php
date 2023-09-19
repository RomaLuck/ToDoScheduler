<?php

namespace App\Telegram\Command;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class StartCommand
{
    public function __invoke(Nutgram $bot): void
    {
        $bot->sendMessage(
            text: 'Welcome!',
            reply_markup: InlineKeyboardMarkup::make()
                ->addRow(
                    InlineKeyboardButton::make('Register', callback_data: 'register')
                )
        );
    }

}