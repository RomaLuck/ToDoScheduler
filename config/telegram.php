<?php
/** @var SergiX44\Nutgram\Nutgram $bot */

use App\Telegram\Command\HelpCommand;
use App\Telegram\Command\StartCommand;
use App\Telegram\Command\TaskCommand;
use App\Telegram\Conversations\CreateTaskConversation;
use App\Telegram\Conversations\RegistrationConversation;
use App\Telegram\Handlers\DeleteUserHandler;
use App\Telegram\Handlers\DisplayTasksHandler;
use App\Telegram\Handlers\ExceptionHandler;
use App\Telegram\Handlers\InfoHandler;
use App\Telegram\Middleware\AuthMiddleware;
use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;

Conversation::refreshOnDeserialize();

$bot->onCommand('start', StartCommand::class);
$bot->onCommand('help', HelpCommand::class);
$bot->onCallbackQueryData('register', RegistrationConversation::class);
$bot->onCallbackQueryData('info', InfoHandler::class);

$bot->group(function (Nutgram $bot) {
    $bot->onCommand('tasks', TaskCommand::class);
    $bot->onText("\xF0\x9F\x95\x9B Create new task", CreateTaskConversation::class);
    $bot->onText("\xF0\x9F\x94\xA5 My tasks", DisplayTasksHandler::class);
    $bot->onCallbackQueryData('delete_user', DeleteUserHandler::class);
})->middleware(AuthMiddleware::class);

$bot->onException(ExceptionHandler::class);

$bot->fallback(function (Nutgram $bot) {
    $bot->sendMessage('Sorry, I don\'t understand.');
});

