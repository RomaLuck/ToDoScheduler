<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use App\Service\CalendarService;
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
        if ($user === null) {
            $this->addFlash('danger', 'User is not authorized');
            return $this->redirectToRoute('app_task');
        }
        $currentWeek = $request->get('week') ?? $calendar->getCurrentWeek();
        $firstDayOfWeek = $calendar->getFirstDayOfWeek($currentWeek, $calendar->getCurrentYear());
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
    #[Route('/calendar/month/{month?}/{year?}', name: 'app_month', methods: ['GET'])]
    public function showMonthCalendar(TaskRepository $repository, Request $request, CalendarService $calendar): Response
    {
        $user = $this->getUser();
        if ($user === null) {
            $this->addFlash('danger', 'User is not authorized');
            return $this->redirectToRoute('app_task');
        }
        $year = $request->get('year') ?? $calendar->getCurrentYear();
        $month = $request->get('month') ?? $calendar->getCurrentMonth();
        $monthTasks = $repository->findUncompletedTasks($user);
        return $this->render('task/month.html.twig', [
            'weekDays' => $calendar->getNamesDaysOfWeek(),
            'dataTimeDayList' => $calendar->getDataTimeDayList($month, $year),
            'month' => $month,
            'monthTasks' => $monthTasks,
            'currentDate' => $calendar->getCurrentDate(),
        ]);
    }

    #[Route('/calendar/next-month', name: 'next_month')]
    public function nextMonth(Request $request, CalendarService $calendar): Response
    {
        $currentMonth = (int)$request->get('month');
        $nextMonth = $currentMonth + 1;
        $currentYear = $calendar->getCurrentYear() + floor($currentMonth / 12);

        return $this->redirectToRoute('app_month', ['month' => $nextMonth, 'year' => $currentYear]);
    }

    #[Route('/calendar/previous-month', name: 'previous_month')]
    public function previousMonth(Request $request, CalendarService $calendar): Response
    {
        $currentMonth = (int)$request->get('month');
        $previousMonth = $currentMonth - 1;
        $currentYear = $calendar->getCurrentYear() + floor(($previousMonth - 1) / 12);
        return $this->redirectToRoute('app_month', ['month' => $previousMonth, 'year' => $currentYear]);
    }
}