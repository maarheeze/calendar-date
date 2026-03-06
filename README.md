# maarheeze/calendar-date

A timezone-agnostic calendar date value object for PHP. No time component, no timezone concerns.

## Requirements

- PHP 8.2+

## Installation

```bash
composer require maarheeze/calendar-date
```

## Usage

### Creating a date

```php
CalendarDate::today();
CalendarDate::parse('2000-01-01');
CalendarDate::parse('today');
CalendarDate::parse('+3 weeks');
CalendarDate::instance($dateTimeInterface);
CalendarDate::createFromFormat('d-m-Y', '01-01-2000');
```

### Formatting

```php
$date->format('Y-m-d');
$date->__toString(); // defaults to Y-m-d
```

### Arithmetic

```php
$date->addDays(7);
$date->subDays(7);
$date->addMonths(1);
$date->subMonths(1);
$date->addYears(1);
$date->subYears(1);
```

All arithmetic methods return a new `CalendarDate` instance.

### Comparison

```php
$date->equalTo($other);
$date->isBefore($other);
$date->isAfter($other);
$date->isToday();
$date->isPast();
$date->isFuture();
```

## License

BSD-3