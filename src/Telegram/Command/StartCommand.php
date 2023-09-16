<?php

namespace App\Telegram\Command;

use SergiX44\Nutgram\Handlers\Type\Command;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class StartCommand extends Command
{
    protected string $command = 'start';

    protected ?string $description = 'Start commands';

    public function handle(Nutgram $bot): void
    {
        $bot->sendMessage(
            text: 'Welcome!',
            reply_markup: InlineKeyboardMarkup::make()
                ->addRow(
                    InlineKeyboardButton::make('Tasks', callback_data: 'tasks'),
                    InlineKeyboardButton::make('Register', callback_data: 'register')
                )
        );
    }

}