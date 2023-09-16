<?php
/** @var SergiX44\Nutgram\Nutgram $bot */

use App\Telegram\Command\StartCommand;
use App\Telegram\Command\TaskCommand;
use App\Telegram\Conversations\CreateTaskConversation;
use App\Telegram\Conversations\RegistrationConversation;
use App\Telegram\Handlers\DisplayTasksHandler;
use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;

Conversation::refreshOnDeserialize();

$bot->onCommand('start', StartCommand::class);
$bot->onCallbackQueryData('tasks', TaskCommand::class);
$bot->onCallbackQueryData('register', RegistrationConversation::class);
$bot->onCallbackQueryData('create_task', CreateTaskConversation::class);
$bot->onCallbackQueryData('show_tasks', DisplayTasksHandler::class);

$bot->fallback(function (Nutgram $bot) {
    $bot->sendMessage('Sorry, I don\'t understand.');
});

$bot->onException(function (Nutgram $bot, \Throwable $exception) {
    error_log($exception);
    $bot->sendMessage('Whoops! '.$exception->getMessage());
});
