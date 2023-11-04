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
    #[Route('/calendar/week/{week?}/{year?}', name: 'app_week', methods: ['GET'])]
    public function showWeeksCalendar(TaskRepository $repository, Request $request, CalendarService $calendar): Response
    {
        $user = $this->getUser();
        if ($user === null) {
            $this->addFlash('danger', 'User is not authorized');
            return $this->redirectToRoute('app_task');
        }
        $currentWeek = $request->get('week') ?? $calendar->getCurrentWeek();
        $year = $request->get('year') ?? $calendar->getCurrentYear();
        $weekTasks = $repository->findUncompletedTasks($user);
        return $this->render('task/week.html.twig', [
            'currentDate' => $calendar->getCurrentDate(),
            'weekTasks' => $weekTasks,
            'week' => $currentWeek,
            'year' => $year,
            'daysOfWeek' => $calendar->getDaysOfWeek($currentWeek, $year),
        ]);
    }

    /**
     * @throws \Exception
     */
    #[Route('/calendar/next-week', name: 'next_week')]
    public function nextWeek(Request $request, CalendarService $calendar): Response
    {
        $currentWeek = $request->get('week');
        $currentYear = $request->get('year') ?? $calendar->getCurrentYear();
        $nextWeek = $calendar->getNextWeek($currentWeek, $currentYear);
        return $this->redirectToRoute('app_week', [
            'week' => $nextWeek->format('W'),
            'year' => $nextWeek->format('Y')
        ]);
    }

    /**
     * @throws \Exception
     */
    #[Route('/calendar/previous-week', name: 'previous_week')]
    public function previousWeek(Request $request, CalendarService $calendar): Response
    {
        $currentWeek = $request->get('week');
        $currentYear = $request->get('year') ?? $calendar->getCurrentYear();
        $previousWeek = $calendar->getPreviousWeek($currentWeek, $currentYear);
        return $this->redirectToRoute('app_week', [
            'week' => $previousWeek->format('W'),
            'year' => $previousWeek->format('Y')
        ]);
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
            'year' => $year,
            'monthTasks' => $monthTasks,
            'currentDate' => $calendar->getCurrentDate(),
        ]);
    }

    /**
     * @throws \Exception
     */
    #[Route('/calendar/next-month', name: 'next_month')]
    public function nextMonth(Request $request, CalendarService $calendar): Response
    {
        $currentMonth = $request->get('month');
        $currentYear = $request->get('year') ?? $calendar->getCurrentYear();
        $nextMonth = $calendar->getNextMonth((int)$currentMonth, (int)$currentYear);
        return $this->redirectToRoute('app_month', [
            'month' => $nextMonth->format('m'),
            'year' => $nextMonth->format('Y')
        ]);
    }

    /**
     * @throws \Exception
     */
    #[Route('/calendar/previous-month', name: 'previous_month')]
    public function previousMonth(Request $request, CalendarService $calendar): Response
    {
        $currentMonth = (int)$request->get('month');
        $currentYear = $request->get('year') ?? $calendar->getCurrentYear();
        $previousMonth = $calendar->getPreviousMonth($currentMonth, $currentYear);
        return $this->redirectToRoute('app_month', [
            'month' => $previousMonth->format('m'),
            'year' => $previousMonth->format('Y')
        ]);
    }
}