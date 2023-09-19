<?php

namespace App\Telegram\Command;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class HelpCommand
{
    public function __invoke(Nutgram $bot): void
    {
        $bot->sendMessage(
            text: 'How can I help you?',
            reply_markup: InlineKeyboardMarkup::make()
                ->addRow(
                    InlineKeyboardButton::make('Register', callback_data: 'register'),
                    InlineKeyboardButton::make('Info', callback_data: 'info')
                )
                ->addRow(
                    InlineKeyboardButton::make('Delete user', callback_data: 'delete_user'),
                )
        );
    }
}