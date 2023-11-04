<?php

namespace App\Service;

use DateTimeImmutable;

class CalendarService
{
    private DateTimeImmutable $dateTime;

    public function __construct()
    {
        $this->dateTime = new DateTimeImmutable();
    }

    public function getCurrentDate(): DateTimeImmutable
    {
        return $this->dateTime;
    }

    /**
     * @throws \Exception
     */
    private function getFirstDayOfCurrentYear(int $year): DateTimeImmutable
    {
        return new DateTimeImmutable("$year-01-01");
    }

    /**
     * @throws \Exception
     */
    private function getFirstWeekOfMonthNumber(int $month, int $year): int
    {
        return $this->getFirstDayOfMonth($month, $year)->format('W');
    }

    /**
     * @throws \Exception
     */
    private function getLastWeekOfMonthNumber(int $month, int $year): int
    {
        return $this->getLastDayOfMonth($month, $year)->format('W');
    }

    /**
     * @throws \Exception
     */
    public function getDataTimeDayList(int $month, $year): array
    {
        $dataTimeDayList = [];
        $weekNumbers = range($this->getFirstWeekOfMonthNumber($month, $year), $this->getLastWeekOfMonthNumber($month, $year));
        foreach ($weekNumbers as $weekNumber) {
            $firstDayOfWeek = $this->getFirstDayOfCurrentYear($year)->modify('+' . ($weekNumber - 1) . ' weeks')->modify('+1 days');
            for ($i = 0; $i < 7; $i++) {
                $dataTimeDayList[$weekNumber][] = $firstDayOfWeek->modify("+$i days");
            }
        }
        return $dataTimeDayList;
    }

    /**
     * @throws \Exception
     */
    public function getFirstDayOfWeek(int $week, int $year): DateTimeImmutable
    {
        return $this->getFirstDayOfCurrentYear($year)->modify('+' . ($week - 1) . ' weeks')
            ->modify('+1 days');
    }

    public function getDaysOfWeek(DateTimeImmutable $firstDayOfWeek): array
    {
        $days = [];
        for ($i = 0; $i <= 6; $i++) {
            $days[] = $firstDayOfWeek->modify('+' . $i . ' days');
        }
        return $days;
    }

    /**
     * @throws \Exception
     */
    private function getFirstDayOfMonth(int $month, $year): DateTimeImmutable
    {
        return $this->getFirstDayOfCurrentYear($year)->modify('+' . ($month - 1) . ' month');
    }

    /**
     * @throws \Exception
     */
    private function getLastDayOfMonth(int $month, int $year): DateTimeImmutable
    {
        $numDaysInMonth = (new DateTimeImmutable($this->getFirstDayOfMonth($month, $year)->format('Y-m-d')))->format('t');
        return $this->getFirstDayOfMonth($month, $year)->modify('+' . (int)$numDaysInMonth - 1 . ' days');
    }

    public function getCurrentYear(): int
    {
        return $this->dateTime->format('Y');
    }

    public function getCurrentWeek(): int
    {
        return $this->dateTime->format('W');
    }

    public function getCurrentMonth(): int
    {
        return $this->dateTime->format('m');
    }

    public function getNamesDaysOfWeek(): array
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