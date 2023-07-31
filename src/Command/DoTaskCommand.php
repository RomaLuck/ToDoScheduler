<?php

namespace App\Command;

use App\Controller\TaskController;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'do:task',
    description: 'Add a short description for your command',
)]
class DoTaskCommand extends Command
{
    public TaskController $taskController;

    public function __construct(TaskController $taskController, string $name = null)
    {
        parent::__construct($name);
        $this->taskController = $taskController;
    }

    protected function configure(): void
    {
//        $this
//            ->addArgument('check', InputArgument::OPTIONAL, 'Check all tasks')
//            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($this->taskController->sendTaskReminders() !== null) {
            $io->success('Massage is sent');
        }

        return Command::SUCCESS;
    }
}
