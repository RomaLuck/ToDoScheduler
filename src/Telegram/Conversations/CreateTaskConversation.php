<?php

namespace App\Telegram\Conversations;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\SimpleCache\InvalidArgumentException;
use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;

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
        $this->next('askMonthDayDeadline');
    }

    /**
     * @throws InvalidArgumentException
     */
    public function askMonthDayDeadline(Nutgram $bot): void
    {
        $this->title = trim(htmlspecialchars($bot->message()->text));
        $bot->sendMessage('Set deadline(Format: month day)');
        $this->next('askHourMinuteDeadline');
    }

    /**
     * @throws InvalidArgumentException
     */
    public function askHourMinuteDeadline(Nutgram $bot): void
    {
        $answer = array_map(
            static fn($value) => $value < 10 ? sprintf("%02d", $value) : $value,
            explode(' ', trim(htmlspecialchars($bot->message()->text)))
        );
        $this->dateTime = array_combine(
            ['month', 'day'],
            $answer
        );
        $bot->sendMessage('Set deadline(Format: hour minute)');
        $this->next('recap');
    }

    /**
     * @throws InvalidArgumentException
     * @throws \Exception
     */
    public function recap(Nutgram $bot): void
    {
        $answer = array_map(
            static fn($value) => $value < 10 ? sprintf("%02d", $value) : $value,
            explode(' ', trim(htmlspecialchars($bot->message()->text)))
        );
        $time = array_combine(
            ['hour', 'minute'],
            $answer
        );
        $dataTime = array_merge($this->dateTime, $time);
        $formattedDataTime = (new \DateTime())->format('Y') . '-' . $dataTime['month'] . '-' . $dataTime['day'] . 'T' . $dataTime['hour'] . ':' . $dataTime['minute'];

        $task = new Task();
        $task->setTitle($this->title);
        $task->setUser($this->entityManager->getRepository(User::class)->findOneBy(['chat_id' => $bot->chatId()]));
        $task->setCreatedAt();
        $task->setDeadLine(\DateTimeImmutable::createFromFormat('Y-m-d\TH:i', $formattedDataTime));
        $task->setStatus(false);
        $this->entityManager->persist($task);
        $this->entityManager->flush();
        $bot->sendMessage(sprintf('Task "%s" has been created on the %s', $this->title, $formattedDataTime));
        $this->end();
    }
}