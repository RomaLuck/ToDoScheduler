<?php

namespace App\Telegram\Conversations;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\SimpleCache\InvalidArgumentException;
use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;

class CreateTaskConversation extends Conversation
{
    protected ?string $step = 'askTaskName';

    protected string $title;
    /**
     * @var string[]
     */
    protected array $dateTime;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function askTaskName(Nutgram $bot): void
    {
        $bot->sendMessage('What task do you want to create?');
        $this->next('checkTitle');
    }

    /**
     * @throws InvalidArgumentException
     */
    public function checkTitle(Nutgram $bot): void
    {
        $this->title = trim(htmlspecialchars($bot->message()->text));
        $this->askMonthDayDeadline($bot);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function askMonthDayDeadline(Nutgram $bot): void
    {
        $bot->sendMessage(
            text: 'Set deadline (Format: month day) . You can also press the buttons',
            reply_markup: ReplyKeyboardMarkup::make(
                resize_keyboard: true,
                one_time_keyboard: true,
            )
                ->addRow(
                    KeyboardButton::make("Today"),
                    KeyboardButton::make("Tomorrow")
                )
        );
        $this->next('checkMonthDayDeadline');
    }

    /**
     * @throws InvalidArgumentException
     */
    public function checkMonthDayDeadline(Nutgram $bot): void
    {
        $answer = trim(htmlspecialchars($bot->message()->text));
        $date = match ($answer) {
            'Today' => (new \DateTimeImmutable())->format('m d'),
            'Tomorrow' => (new \DateTimeImmutable())->modify('+1 days')->format('m d'),
            default => $answer,
        };

        if (preg_match('!^(?<month>\d+) (?<day>\d+)$!iu', $date, $dateMatch)) {
            $month = sprintf("%02d", $dateMatch['month']);
            $day = sprintf("%02d", $dateMatch['day']);
        } else {
            $bot->sendMessage('Wrong format');
            $this->askMonthDayDeadline($bot);
            return;
        }

        if (!checkdate($month, $day, (new \DateTime())->format('Y'))) {
            $bot->sendMessage('Wrong date');
            $this->askMonthDayDeadline($bot);
            return;
        }

        $this->dateTime['month'] = $month;
        $this->dateTime['day'] = $day;
        $this->askHourMinuteDeadline($bot);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function askHourMinuteDeadline(Nutgram $bot): void
    {
        $bot->sendMessage('Set deadline(Format: hour minute)');
        $this->next('checkHourMinuteDeadline');
    }

    /**
     * @throws InvalidArgumentException
     */
    public function checkHourMinuteDeadline(Nutgram $bot): void
    {
        $answer = trim(htmlspecialchars($bot->message()->text));

        if (preg_match('!^(?<hour>([01]\d|2[0-3])) (?<minute>[0-5]\d)$!iu', $answer, $timeMatch)) {
            $hour = sprintf("%02d", $timeMatch['hour']);
            $minute = sprintf("%02d", $timeMatch['minute']);
        } else {
            $bot->sendMessage('Wrong time');
            $this->askMonthDayDeadline($bot);
            return;
        }
        $this->dateTime['hour'] = $hour;
        $this->dateTime['minute'] = $minute;
        $this->recap($bot);
    }

    /**
     * @throws InvalidArgumentException
     * @throws \Exception
     */
    public function recap(Nutgram $bot): void
    {
        $formattedDataTime = (new \DateTime())->format('Y') . '-' . $this->dateTime['month'] . '-' . $this->dateTime['day'] . 'T' . $this->dateTime['hour'] . ':' . $this->dateTime['minute'];

        $task = new Task();
        $task->setTitle($this->title);
        $task->setUser($this->entityManager->getRepository(User::class)->findOneBy(['chat_id' => $bot->chatId()]));
        $task->setCreatedAt();
        $task->setDeadLine(\DateTimeImmutable::createFromFormat('Y-m-d\TH:i', $formattedDataTime));
        $task->setStatus(false);
        $this->entityManager->persist($task);
        $this->entityManager->flush();
        $bot->sendMessage(
            text: sprintf('Task "%s" has been created on the %s', $this->title, $formattedDataTime),
            reply_markup: ReplyKeyboardMarkup::make(
                resize_keyboard: true,
            )
                ->addRow(
                    KeyboardButton::make("\xF0\x9F\x95\x9B Create new task"),
                    KeyboardButton::make("\xF0\x9F\x94\xA5 My tasks")
                )
        );
        $this->end();
    }
}