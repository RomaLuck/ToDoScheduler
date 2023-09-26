<?php

namespace App\Telegram\Command;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;

class TaskCommand
{
    public function __invoke(Nutgram $bot): void
    {
        $bot->sendMessage(
            text: 'Choose action!',
            reply_markup: ReplyKeyboardMarkup::make(
                resize_keyboard: true,
            )
                ->addRow(
                    KeyboardButton::make("\xF0\x9F\x95\x9B Create new task"),
                    KeyboardButton::make("\xF0\x9F\x94\xA5 My tasks")
                )
        );
    }
}