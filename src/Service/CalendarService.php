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
    public function getFirstWeekOfMonthNumber(int $month, int $year): int
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
    public function getDataTimeDayList(int $month, int $year): array
    {
        $dataTimeDayList = [];
        $weekNumbers = range($this->getFirstWeekOfMonthNumber($month, $year), $this->getLastWeekOfMonthNumber($month, $year));
        foreach ($weekNumbers as $weekNumber) {
            $firstDayOfWeek = $this->getFirstDayOfWeek($weekNumber, $year);
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
        return new DateTimeImmutable($year . '-W' . sprintf("%02d", $week));
    }

    /**
     * @throws \Exception
     */
    public function getDaysOfWeek(int $week, int $year): array
    {
        $days = [];
        for ($i = 0; $i <= 6; $i++) {
            $days[] = $this->getFirstDayOfWeek($week, $year)->modify('+' . $i . ' days');
        }
        return $days;
    }

    /**
     * @throws \Exception
     */
    public function getFirstDayOfMonth(int $month, int $year): DateTimeImmutable
    {
        return new DateTimeImmutable($year . '-' . $month);
    }

    /**
     * @throws \Exception
     */
    private function getLastDayOfMonth(int $month, int $year): DateTimeImmutable
    {
        return $this->getFirstDayOfMonth($month, $year)->modify('+' . $this->getNumDaysInMonth($month, $year) - 1 . ' days');
    }

    /**
     * @throws \Exception
     */
    public function getNextWeek(int $week, int $year): DateTimeImmutable
    {
        return $this->getFirstDayOfWeek($week, $year)->modify('+1 week');
    }

    /**
     * @throws \Exception
     */
    public function getPreviousWeek(int $week, int $year): DateTimeImmutable
    {
        return $this->getFirstDayOfWeek($week, $year)->modify('-1 week');
    }

    /**
     * @throws \Exception
     */
    public function getNextMonth(int $month, int $year): DateTimeImmutable
    {
        return $this->getFirstDayOfMonth($month, $year)->modify('+1 month');
    }

    /**
     * @throws \Exception
     */
    public function getPreviousMonth(int $month, int $year): DateTimeImmutable
    {
        return $this->getFirstDayOfMonth($month, $year)->modify('-1 month');
    }

    /**
     * @throws \Exception
     */
    private function getNumDaysInMonth(int $month, int $year): int
    {
        return $this->getFirstDayOfMonth($month, $year)->format('t');
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