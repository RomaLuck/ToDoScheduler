<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CalendarController extends AbstractController
{
    /**
     * @throws \Exception
     */
    #[Route('/calendar/week/{week?}', name: 'app_calendar', methods: ['GET'])]
    public function showCalendar(TaskRepository $repository, Request $request): Response
    {
        $user = $this->getUser();
        $currentDate = new DateTimeImmutable();
        $currentYear = $currentDate->format('Y');
        $currentWeekDefault = $currentDate->format('W');
        $currentWeek = $request->get('week') ?? $currentWeekDefault;
        $firstDayOfYear = new DateTimeImmutable("$currentYear-01-01");
        $firstDayOfWeek = $firstDayOfYear->modify('+' . ($currentWeek - 1) . ' weeks');
        $weekTasks = $repository->findUncompletedTasks($user);

        return $this->render('task/calendar.html.twig', [
            'weekDays' => $this->getDaysOfWeek(),
            'currentDate' => $currentDate,
            'firstDayOfWeek' => $firstDayOfWeek,
            'weekTasks' => $weekTasks,
            'week' => $currentWeek
        ]);
    }

    #[Route('/calendar/next-week', name: 'next_week')]
    public function nextWeek(Request $request): Response
    {
        $currentWeek = $request->get('week');
        return $this->redirectToRoute('app_calendar', ['week' => $currentWeek + 1]);
    }

    #[Route('/calendar/previous-week', name: 'previous_week')]
    public function previousWeek(Request $request): Response
    {
        $currentWeek = $request->get('week');
        return $this->redirectToRoute('app_calendar', ['week' => $currentWeek - 1]);
    }

    public function getDaysOfWeek(): array
    {
        return [
            'Sunday',
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday',
        ];
    }
}