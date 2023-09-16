<?php

namespace App\Telegram\Command;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class TaskCommand
{
    public function __invoke(Nutgram $bot): void
    {
        $bot->sendMessage(
            text: 'Choose action!',
            reply_markup: InlineKeyboardMarkup::make()
                ->addRow(
                    InlineKeyboardButton::make('Create new task', callback_data: 'create_task'),
                    InlineKeyboardButton::make('Your tasks', callback_data: 'show_tasks')
                )
        );
    }
}