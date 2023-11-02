<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use App\Service\CalendarService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class CalendarController extends AbstractController
{
    /**
     * @throws \Exception
     */
    #[Route('/calendar/week/{week?}', name: 'app_week', methods: ['GET'])]
    public function showWeeksCalendar(TaskRepository $repository, Request $request, CalendarService $calendar): Response
    {
        $user = $this->getUser();
        $currentWeek = $request->get('week') ?? $calendar->getCurrentWeek();
        $firstDayOfWeek = $calendar->getFirstDayOfWeek($currentWeek);
        $weekTasks = $repository->findUncompletedTasks($user);

        return $this->render('task/week.html.twig', [
            'currentDate' => $calendar->getCurrentDate(),
            'weekTasks' => $weekTasks,
            'week' => $currentWeek,
            'daysOfWeek' => $calendar->getDaysOfWeek($firstDayOfWeek),
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
    public function showMonthCalendar(TaskRepository $repository, Request $request, CalendarService $calendar): Response
    {
        $user = $this->getUser();
        $month = $request->get('month') ?? $calendar->getCurrentMonth();
        $monthTasks = $repository->findUncompletedTasks($user);
        return $this->render('task/month.html.twig', [
            'weekDays' => $calendar->getNamesDaysOfWeek(),
            'dataTimeDayList' => $calendar->getDataTimeDayList($month),
            'month' => $month,
            'monthTasks' => $monthTasks,
            'currentDate' => $calendar->getCurrentDate(),
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
}