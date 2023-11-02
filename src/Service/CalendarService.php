<?php

namespace App\Service;

use DateTimeImmutable;

class CalendarService
{
    private $year;
    private $month;
    private $day;
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
    public function getFirstDayOfCurrentYear(): DateTimeImmutable
    {
        return new DateTimeImmutable($this->getCurrentYear() . "-01-01");
    }

    /**
     * @throws \Exception
     */
    public function getLastDayOfCurrentYear(): DateTimeImmutable
    {
        return new DateTimeImmutable($this->getCurrentYear() . "-12-31");
    }

    /**
     * @throws \Exception
     */
    public function getFirstWeekOfMonthNumber(string $month): int
    {
        return $this->getFirstDayOfMonth($month)->format('W');
    }

    /**
     * @throws \Exception
     */
    public function getLastWeekOfMonthNumber(string $month): int
    {
        return $this->getLastDayOfMonth($month)->format('W');
    }

    /**
     * @throws \Exception
     */
    public function getDataTimeDayList(string $month): array
    {
        $dataTimeDayList = [];
        $weekNumbers = range($this->getFirstWeekOfMonthNumber($month), $this->getLastWeekOfMonthNumber($month));
        foreach ($weekNumbers as $weekNumber) {
            $firstDayOfWeek = $this->getFirstDayOfCurrentYear()->modify('+' . ($weekNumber - 1) . ' weeks')->modify('+1 days');
            for ($i = 0; $i < 7; $i++) {
                $dataTimeDayList[$weekNumber][] = $firstDayOfWeek->modify("+$i days");
            }
        }
        return $dataTimeDayList;
    }

    /**
     * @throws \Exception
     */
    public function getFirstDayOfWeek($week): DateTimeImmutable
    {
        return $this->getFirstDayOfCurrentYear()->modify('+' . ($week - 1) . ' weeks')
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
    public function getFirstDayOfMonth(string $month): DateTimeImmutable
    {
        return new DateTimeImmutable($this->getCurrentYear() . "-$month-01");
    }

    /**
     * @throws \Exception
     */
    public function getLastDayOfMonth(string $month): DateTimeImmutable
    {
        return new DateTimeImmutable($this->getCurrentYear() . "-$month-" . $this->dateTime->format('t'));
    }

    public function getCurrentYear(): string
    {
        return $this->dateTime->format('Y');
    }

    public function getCurrentWeek(): string
    {
        return $this->dateTime->format('W');
    }

    public function getCurrentMonth(): string
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