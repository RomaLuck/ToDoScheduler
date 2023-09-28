<?php

namespace App\Controller;

use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CalendarController extends AbstractController
{
    /**
     * @throws \Exception
     */
    #[Route('/calendar', name: 'app_calendar')]
    public function showCalendar(): Response
    {
        $currentDate = new DateTimeImmutable();
        $dayOfWeek = $currentDate->format('w');
        $firstDayOfWeek = $currentDate->sub(new \DateInterval('P' . $dayOfWeek . 'D'));
        $lastDayOfWeek = $currentDate->add(new \DateInterval('P' . (6 - $dayOfWeek) . 'D'));

        return $this->render('task/calendar.html.twig', [
            'weekDay' => $this->getDayOfWeek(),
            'currentDate' => $currentDate,
            'firstDayOfWeek' => $firstDayOfWeek,
            'lastDayOfWeek' => $lastDayOfWeek,
        ]);
    }

    public function getDayOfWeek(): array
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