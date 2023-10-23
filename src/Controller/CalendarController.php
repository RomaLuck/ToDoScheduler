<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use DateInterval;
use DatePeriod;
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
    #[Route('/calendar/week/{week?}', name: 'app_week', methods: ['GET'])]
    public function showWeeksCalendar(TaskRepository $repository, Request $request): Response
    {
        $user = $this->getUser();
        $currentDate = new DateTimeImmutable();
        $currentYear = $currentDate->format('Y');
        $currentWeekDefault = $currentDate->format('W');
        $currentWeek = $request->get('week') ?? $currentWeekDefault;
        $firstDayOfYear = new DateTimeImmutable("$currentYear-01-01");
        $firstDayOfWeek = $firstDayOfYear->modify('+' . ($currentWeek - 1) . ' weeks')->modify('+1 days');
        $weekTasks = $repository->findUncompletedTasks($user);

        return $this->render('task/week.html.twig', [
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
        return $this->redirectToRoute('app_week', ['week' => $currentWeek + 1]);
    }

    #[Route('/calendar/previous-week', name: 'previous_week')]
    public function previousWeek(Request $request): Response
    {
        $currentWeek = $request->get('week');
        return $this->redirectToRoute('app_week', ['week' => $currentWeek - 1]);
    }

    /**
     * @throws \Exception
     */
    #[Route('/calendar/month/{month?}', name: 'app_month', methods: ['GET'])]
    public function showMonthCalendar(TaskRepository $repository, Request $request): Response
    {
        $user = $this->getUser();
        $currentDate = new DateTimeImmutable();
        $year = $currentDate->format('Y');
        $currentMonthDefault = $currentDate->format('m');
        $month = $request->get('month') ?? $currentMonthDefault;
        $firstDayOfMonth = new DateTimeImmutable("$year-$month-01");
        $lastDayOfMonth = new DateTimeImmutable("$year-$month-" . $firstDayOfMonth->format('t'));
        $firstDayOfYear = new DateTimeImmutable("$year-01-01");
        $firstWeekNumber = $firstDayOfMonth->format('W');
        $lastWeekNumber = $lastDayOfMonth->format('W');
        $weekNumbers = range($firstWeekNumber, $lastWeekNumber);
        $monthTasks = $repository->findUncompletedTasks($user);

        $dataTimeDayList = [];
        foreach ($weekNumbers as $weekNumber) {
            $firstDayOfWeek = $firstDayOfYear->modify('+' . ($weekNumber - 1) . ' weeks')->modify('+1 days');
            for ($i = 0; $i < 7; $i++) {
                $dataTimeDayList[$weekNumber][] = $firstDayOfWeek->modify("+$i days");
            }
        }

        return $this->render('task/month.html.twig', [
            'weekDays' => $this->getDaysOfWeek(),
            'dataTimeDayList' => $dataTimeDayList,
            'month' => $month,
            'monthTasks' => $monthTasks
        ]);
    }

    #[Route('/calendar/next-month', name: 'next_month')]
    public function nextMonth(Request $request): Response
    {
        $currentMonth = $request->get('month');
        return $this->redirectToRoute('app_month', ['month' => $currentMonth + 1]);
    }

    #[Route('/calendar/previous-month', name: 'previous_month')]
    public function previousMonth(Request $request): Response
    {
        $currentMonth = $request->get('month');
        return $this->redirectToRoute('app_month', ['month' => $currentMonth - 1]);
    }

    public function getDaysOfWeek(): array
    {
        return [
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday',
            'Sunday',
        ];
    }
}