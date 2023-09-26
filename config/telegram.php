<?php
/** @var SergiX44\Nutgram\Nutgram $bot */

use App\Telegram\Command\HelpCommand;
use App\Telegram\Command\StartCommand;
use App\Telegram\Command\TaskCommand;
use App\Telegram\Conversations\CreateTaskConversation;
use App\Telegram\Conversations\RegistrationConversation;
use App\Telegram\Handlers\DeleteUserHandler;
use App\Telegram\Handlers\DisplayTasksHandler;
use App\Telegram\Handlers\InfoHandler;
use App\Telegram\Middleware\AuthMiddleware;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;

Conversation::refreshOnDeserialize();

$bot->onCommand('start', StartCommand::class);
$bot->onCommand('help', HelpCommand::class);
$bot->onCallbackQueryData('register', RegistrationConversation::class);
$bot->onCallbackQueryData('info', InfoHandler::class);

$bot->group(function (Nutgram $bot) {
    $bot->onCommand('tasks', TaskCommand::class);
    $bot->onText('Create new task', CreateTaskConversation::class);
    $bot->onText('My tasks', DisplayTasksHandler::class);
    $bot->onCallbackQueryData('delete_user', DeleteUserHandler::class);
})->middleware(AuthMiddleware::class);

$bot->fallback(function (Nutgram $bot) {
    $bot->sendMessage('Sorry, I don\'t understand.');
});

$bot->onException(UniqueConstraintViolationException::class, function (Nutgram $bot, UniqueConstraintViolationException $exception) {
    $bot->sendMessage('Such email already exists. Try again, please');
});

$bot->onException(function (Nutgram $bot, \Throwable $exception) {
    error_log($exception);
    $bot->sendMessage('Whoops! ' . $exception->getMessage());
});
