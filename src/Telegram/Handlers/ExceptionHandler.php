<?php

namespace App\Telegram\Handlers;

use Psr\Log\LoggerInterface;
use SergiX44\Nutgram\Nutgram;

class ExceptionHandler
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function __invoke(Nutgram $bot, \Throwable $exception): void
    {
        $bot->sendMessage($exception, $_ENV['ADMIN_CHAT_ID']);
        $this->logger->error($exception->getMessage());
    }
}