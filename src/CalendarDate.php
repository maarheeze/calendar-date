<?php

declare(strict_types=1);

namespace Maarheeze\CalendarDate;

use DateMalformedStringException;
use DateTimeImmutable;
use DateTimeInterface;
use Stringable;

use function sprintf;

class CalendarDate implements Stringable
{
    public const DEFAULT_STRING_FORMAT = 'Y-m-d';

    private DateTimeImmutable $date;

    private function __construct(
        DateTimeImmutable $date,
    ) {
        $this->date = $date->setTime(0, 0);
    }

    public static function today(): self
    {
        return new self(new DateTimeImmutable('today'));
    }

    public static function instance(DateTimeInterface $date): self
    {
        return new self(DateTimeImmutable::createFromInterface($date));
    }

    public static function createFromFormat(string $format, string $date): self
    {
        $result = DateTimeImmutable::createFromFormat($format, $date);

        if ($result === false) {
            throw new CalendarDateException('Unable to create CalendarDate from format');
        }

        return new self($result);
    }

    /**
     * @throws CalendarDateException
     */
    public static function parse(string $time): self
    {
        try {
            return self::instance(new DateTimeImmutable($time));
        } catch (DateMalformedStringException $previous) {
            throw new CalendarDateException($previous->getMessage(), $previous->getCode(), $previous);
        }
    }

    public function __toString(): string
    {
        return $this->format(self::DEFAULT_STRING_FORMAT);
    }

    public function format(string $format): string
    {
        return $this->date->format($format);
    }

    public function equalTo(CalendarDate $date): bool
    {
        return $this->format(self::DEFAULT_STRING_FORMAT) === $date->format(self::DEFAULT_STRING_FORMAT);
    }

    public function isBefore(CalendarDate $date): bool
    {
        return $this->format(self::DEFAULT_STRING_FORMAT) < $date->format(self::DEFAULT_STRING_FORMAT);
    }

    public function isAfter(CalendarDate $date): bool
    {
        return $this->format(self::DEFAULT_STRING_FORMAT) > $date->format(self::DEFAULT_STRING_FORMAT);
    }

    public function isToday(): bool
    {
        return $this->equalTo(self::today());
    }

    public function isPast(): bool
    {
        return $this->isBefore(self::today());
    }

    public function isFuture(): bool
    {
        return $this->isAfter(self::today());
    }

    /**
     * @throws CalendarDateException
     */
    public function addDays(int $value): self
    {
        return $this->modify(sprintf('+%d days', $value));
    }

    /**
     * @throws CalendarDateException
     */
    public function subDays(int $value): self
    {
        return $this->modify(sprintf('-%d days', $value));
    }

    /**
     * @throws CalendarDateException
     */
    public function addMonths(int $value): self
    {
        return $this->modify(sprintf('+%d months', $value));
    }

    /**
     * @throws CalendarDateException
     */
    public function subMonths(int $value): self
    {
        return $this->modify(sprintf('-%d months', $value));
    }

    /**
     * @throws CalendarDateException
     */
    public function addYears(int $value): self
    {
        return $this->modify(sprintf('+%d years', $value));
    }

    /**
     * @throws CalendarDateException
     */
    public function subYears(int $value): self
    {
        return $this->modify(sprintf('-%d years', $value));
    }

    private function modify(string $modifier): self
    {
        try {
            return new self($this->date->modify($modifier));
        } catch (DateMalformedStringException $previous) {
            throw new CalendarDateException($previous->getMessage(), $previous->getCode(), $previous);
        }
    }
}
