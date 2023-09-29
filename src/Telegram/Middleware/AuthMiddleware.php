<?php

namespace App\Telegram\Middleware;

use App\Repository\UserRepository;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class AuthMiddleware
{
    public function __construct(private readonly UserRepository $repository)
    {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(Nutgram $bot, $next): void
    {
        if ($this->repository->findOneBy(['chat_id' => $bot->chatId()]) === null) {
            $bot->sendMessage(
                text: 'Register, please!',
                reply_markup: InlineKeyboardMarkup::make()
                    ->addRow(
                        InlineKeyboardButton::make('Register', callback_data: 'register')
                    )
            );
            return;
        }
        $next($bot);
    }
}