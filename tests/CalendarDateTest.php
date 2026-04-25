<?php

declare(strict_types=1);

namespace Tests;

use DateTimeImmutable;
use Maarheeze\CalendarDate\CalendarDate;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function rand;

class CalendarDateTest extends TestCase
{
    public function testInstantiate(): void
    {
        $dateTime = new DateTimeImmutable('now');

        $calendarDate = CalendarDate::instance($dateTime);

        $this->assertEquals($dateTime->format('Y-m-d'), $calendarDate->format('Y-m-d'));
    }

    public function testCreateFromFormat(): void
    {
        $dateTime = new DateTimeImmutable($this->randomDate());

        $calendarDate = CalendarDate::createFromFormat('Y-m-d', $dateTime->format('Y-m-d'));

        $this->assertEquals($dateTime->format('Y-m-d'), $calendarDate->format('Y-m-d'));
    }

    public function testCastToString(): void
    {
        $dateTime = new DateTimeImmutable($this->randomDate());

        $calendarDate = CalendarDate::instance($dateTime);

        $this->assertEquals($dateTime->format('Y-m-d'), $calendarDate->__toString());
    }

    public function testToStringEqualsStringable(): void
    {
        $dateTime = new DateTimeImmutable($this->randomDate());

        $calendarDate = CalendarDate::instance($dateTime);

        $this->assertEquals($calendarDate->__toString(), $calendarDate->toString());
    }

    public function testAddDays(): void
    {
        $calendarDate = CalendarDate::createFromFormat('Y-m-d', '2000-01-01');

        $this->assertEquals('2000-01-08', $calendarDate->addDays(7)->format('Y-m-d'));
    }

    public function testSubDays(): void
    {
        $calendarDate = CalendarDate::createFromFormat('Y-m-d', '2000-01-08');

        $this->assertEquals('2000-01-01', $calendarDate->subDays(7)->format('Y-m-d'));
    }

    public function testAddMonths(): void
    {
        $calendarDate = CalendarDate::createFromFormat('Y-m-d', '2000-01-01');
        $monthsToAdd = rand(1, 11);

        $newCalendarDate = $calendarDate->addMonths($monthsToAdd);

        $this->assertEquals(
            $calendarDate->addMonths($monthsToAdd)->format('Y-m-d'),
            $newCalendarDate->format('Y-m-d'),
        );
    }

    public function testSubMonths(): void
    {
        $calendarDate = CalendarDate::createFromFormat('Y-m-d', '2000-06-01');

        $this->assertEquals('2000-03-01', $calendarDate->subMonths(3)->format('Y-m-d'));
    }

    public function testAddYears(): void
    {
        $calendarDate = CalendarDate::createFromFormat('Y-m-d', '2000-01-01');

        $this->assertEquals('2003-01-01', $calendarDate->addYears(3)->format('Y-m-d'));
    }

    public function testSubYears(): void
    {
        $calendarDate = CalendarDate::createFromFormat('Y-m-d', '2000-01-01');

        $this->assertEquals('1997-01-01', $calendarDate->subYears(3)->format('Y-m-d'));
    }

    public function testEqualDates(): void
    {
        $time = $this->randomDate();

        $date1 = CalendarDate::createFromFormat('Y-m-d', $time);
        $date2 = CalendarDate::createFromFormat('Y-m-d', $time);

        $this->assertTrue($date1->equalTo($date2));
    }

    public function testUnequalDates(): void
    {
        $date1 = CalendarDate::createFromFormat('Y-m-d', '2000-01-01');
        $date2 = CalendarDate::createFromFormat('Y-m-d', '2000-01-02');

        $this->assertFalse($date1->equalTo($date2));
    }

    public function testIsBefore(): void
    {
        $date1 = CalendarDate::createFromFormat('Y-m-d', '2000-01-01');
        $date2 = CalendarDate::createFromFormat('Y-m-d', '2000-01-02');

        $this->assertTrue($date1->isBefore($date2));
        $this->assertFalse($date2->isBefore($date1));
    }

    public function testIsAfter(): void
    {
        $date1 = CalendarDate::createFromFormat('Y-m-d', '2000-01-02');
        $date2 = CalendarDate::createFromFormat('Y-m-d', '2000-01-01');

        $this->assertTrue($date1->isAfter($date2));
        $this->assertFalse($date2->isAfter($date1));
    }

    public function testIsBeforeReturnsFalseForEqualDates(): void
    {
        $date1 = CalendarDate::createFromFormat('Y-m-d', '2000-01-01');
        $date2 = CalendarDate::createFromFormat('Y-m-d', '2000-01-01');

        $this->assertFalse($date1->isBefore($date2));
    }

    public function testIsAfterReturnsFalseForEqualDates(): void
    {
        $date1 = CalendarDate::createFromFormat('Y-m-d', '2000-01-01');
        $date2 = CalendarDate::createFromFormat('Y-m-d', '2000-01-01');

        $this->assertFalse($date1->isAfter($date2));
    }

    #[DataProvider('formatProvider')]
    public function testFormat(string $format): void
    {
        $date = $this->randomDate();
        $calendarDate = CalendarDate::createFromFormat('Y-m-d', $date);

        $this->assertEquals(
            (new DateTimeImmutable($date))->format($format),
            $calendarDate->format($format),
        );
    }

    /**
     * @return array<string[]>
     */
    public static function formatProvider(): array
    {
        return [
            ['Y-m-d'],
            ['d-m-Y'],
            ['d/m/Y'],
        ];
    }

    public function testIsPastForHistoricalDate(): void
    {
        $this->assertTrue(
            CalendarDate::createFromFormat('Y-m-d', '2000-01-01')->isPast(),
        );
    }

    public function testIsPastReturnsFalseForToday(): void
    {
        $this->assertFalse(CalendarDate::today()->isPast());
    }

    public function testIsPastReturnsFalseForFutureDate(): void
    {
        $futureDate = CalendarDate::parse('+' . rand(1, 365) . ' days');

        $this->assertFalse($futureDate->isPast());
    }

    public function testIsFuture(): void
    {
        $futureDate = CalendarDate::parse('+' . rand(1, 365) . ' days');

        $this->assertTrue($futureDate->isFuture());
    }

    public function testIsFutureReturnsFalseForToday(): void
    {
        $this->assertFalse(CalendarDate::today()->isFuture());
    }

    public function testIsFutureReturnsFalseForHistoricalDate(): void
    {
        $this->assertFalse(
            CalendarDate::createFromFormat('Y-m-d', '2000-01-01')->isFuture(),
        );
    }

    public function testIsToday(): void
    {
        $this->assertTrue(CalendarDate::today()->isToday());
    }

    public function testIsTodayReturnsFalseForOtherDate(): void
    {
        $this->assertFalse(
            CalendarDate::createFromFormat('Y-m-d', '2000-01-01')->isToday(),
        );
    }

    public function testToday(): void
    {
        $this->assertEquals(
            (new DateTimeImmutable('today'))->format('Y-m-d'),
            CalendarDate::today()->format('Y-m-d'),
        );
    }

    public function testParse(): void
    {
        $date = $this->randomDate();

        $this->assertEquals($date, CalendarDate::parse($date)->format('Y-m-d'));
    }

    public function testParseRelativeDate(): void
    {
        $this->assertEquals(
            (new DateTimeImmutable('today'))->format('Y-m-d'),
            CalendarDate::parse('today')->format('Y-m-d'),
        );
    }

    private function randomDate(): string
    {
        return (new DateTimeImmutable())
            ->setDate(rand(1970, 2020), rand(1, 12), rand(1, 28))
            ->format('Y-m-d');
    }
}
