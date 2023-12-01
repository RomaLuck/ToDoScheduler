<?php

namespace App\Telegram\Handlers;

use SergiX44\Nutgram\Nutgram;

class InfoHandler
{
    public function __invoke(Nutgram $bot): void
    {
        $bot->sendMessage('Some text');
    }
}