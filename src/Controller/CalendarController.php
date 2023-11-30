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
#[Route('{_locale<%app.supported.locales%>}')]
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

        $week = $request->get('week') ?? $calendar->getCurrentWeek();
        $year = $request->get('year') ?? $calendar->getCurrentYear();
        $nextWeek = $calendar->getNextWeek($week, $year);
        $previousWeek = $calendar->getPreviousWeek($week, $year);
        $weekTasks = $repository->findUncompletedTasks($user);

        return $this->render('task/week.html.twig', [
            'currentDate' => $calendar->getCurrentDate(),
            'weekTasks' => $weekTasks,
            'week' => $week,
            'year' => $year,
            'next_week' => $nextWeek->format('W'),
            'next_year' => $nextWeek->format('Y'),
            'previous_week' => $previousWeek->format('W'),
            'previous_year' => $previousWeek->format('Y'),
            'daysOfWeek' => $calendar->getDaysOfWeek($week, $year),
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
        $nextMonth = $calendar->getNextMonth((int)$month, (int)$year);
        $previousMonth = $calendar->getPreviousMonth($month, $year);
        $monthTasks = $repository->findUncompletedTasks($user);

        return $this->render('task/month.html.twig', [
            'weekDays' => $calendar->getNamesDaysOfWeek(),
            'dateTimeDayList' => $calendar->getDateTimeDayList($month, $year),
            'month' => $month,
            'year' => $year,
            'next_month' => $nextMonth->format('m'),
            'next_year' => $nextMonth->format('Y'),
            'previous_month' => $previousMonth->format('m'),
            'previous_year' => $previousMonth->format('Y'),
            'monthTasks' => $monthTasks,
            'currentDate' => $calendar->getCurrentDate(),
        ]);
    }
}