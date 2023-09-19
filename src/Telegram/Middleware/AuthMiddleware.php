<?php

namespace App\Telegram\Middleware;

use App\Repository\UserRepository;
use SergiX44\Nutgram\Nutgram;

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
        if (!in_array($bot->chatId(), array_map(static fn($user) => $user->getChatId(), $this->repository->findAll()))) {
            throw new \Exception('Register, please');
        }
        $next($bot);
    }
}