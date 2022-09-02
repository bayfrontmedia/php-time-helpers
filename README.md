## PHP time helpers

Helper class to provide useful time related functions.

- [License](#license)
- [Author](#author)
- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)

## License

This project is open source and available under the [MIT License](LICENSE).

## Author

<img src="https://cdn1.onbayfront.com/bfm/brand/bfm-logo.svg" alt="Bayfront Media" width="250" />

- [Bayfront Media homepage](https://www.bayfrontmedia.com?utm_source=github&amp;utm_medium=direct)
- [Bayfront Media GitHub](https://github.com/bayfrontmedia)

## Requirements

* PHP >= 7.1.0

## Installation

```
composer require bayfrontmedia/php-time-helpers
```

## Usage

- [getReadTime](#getreadtime)
- [getDateTime](#getdatetime)
- [isLeapYear](#isleapyear)
- [humanArray](#humanarray)
- [human](#human)
- [isTimezone](#istimezone)
- [isFormat](#isformat)
- [inPast](#inpast)
- [inFuture](#infuture)
- [isBefore](#isbefore)
- [isAfter](#isafter)
- [stopwatch](#stopwatch)

<hr />

### getReadTime

**Description:**

Get estimated minutes necessary to read content, based on reading a given amount of words per minute (WPM).

**Parameters:**

- `$content` (string)
- `$wpm = 180` (int)

**Returns:**

- (int)

**Example:**

```
use Bayfront\TimeHelpers\Time;

$content = 'This is a string of content.';

echo Time::getReadTime($content);

```

<hr />

### getDateTime

**Description:**

Returns datetime of a given timestamp, or current time (default).

**Parameters:**

- `$timestamp = NULL` (int|null)

**Returns:**

- (string)

**Example:**

```
use Bayfront\TimeHelpers\Time;

echo Time::getDateTime();

```

<hr />

### isLeapYear

**Description:**

Checks if a given year is a leap year, using current year by default.

**Parameters:**

- `$year = NULL` (int|null): Four digit year, PHP `date('Y')` format

**Returns:**

- (bool)

**Example:**

```
use Bayfront\TimeHelpers\Time;

if (Time::isLeapYear()) {
    // Do something
}
```

<hr />

### humanArray

**Description:**

Returns human time as an array.

**NOTE:** Due to discrepancies between the length of certain months and years (ie: leap year), elapsed time calculations for these units of time are approximate (30 days per month, 365 days per year).

**Parameters:**

- `$time_start` (int): Timestamp of starting time
- `$time_end` (int): Timestamp of ending time
- `$limit = 'year'` (string): Limit of time duration to calculate
- `$language = NULL` (array|null): Custom language to return

Valid `$limit` values are:

- `year`
- `month`
- `week`
- `day`
- `hour`
- `minute`
- `second`

Passing a `$language` array allows you to translate the words returned by this method. The array keys must match those of the default array, which is:

```
$language = [
    'year' => 'year',
    'years' => 'years',
    'month' => 'month',
    'months' => 'months',
    'week' => 'week',
    'weeks' => 'weeks',
    'day' => 'day',
    'days' => 'days',
    'hour' => 'hour',
    'hours' => 'hours',
    'minute' => 'minute',
    'minutes' => 'minutes',
    'second' => 'second',
    'seconds' => 'seconds',
    'past' => 'ago',
    'present' => 'just now',
    'future' => 'to go'
];
```

**Returns:**

- (array)

#### Example:

```
use Bayfront\TimeHelpers\Time;

$start = time();
$end = time() + 51001;

print_r(Time::humanArray($start, $end, 'minute'));
```

<hr />

### human

**Description:**

Returns human time as a string.

For more information, see [humanArray](#humanarray).

**Parameters:**

- `$time_start` (int): Timestamp of starting time
- `$time_end` (int): Timestamp of ending time
- `$limit = 'year'` (string): Limit of time duration to calculate
- `$language = NULL` (array| null): Custom language to return

**Returns:**

- (string)

**Example:**

```
use Bayfront\TimeHelpers\Time;

$start = time();
$end = time() + 51001;

echo Time::human($start, $end);
```

<hr />

### isTimezone

**Description:**

Checks if string is a valid timezone identifier.

See: [https://www.php.net/manual/en/timezones.php](https://www.php.net/manual/en/timezones.php)

**Parameters:**

- `$timezone` (string)

**Returns:**

- (bool)

**Example:**

```
use Bayfront\TimeHelpers\Time;

if (Time::isTimezone('America/New_York')) {
    // Do something
}
```

<hr />

### isFormat

**Description:**

Checks if value is a given dateTime format.

See: [https://www.php.net/manual/en/function.date.php](https://www.php.net/manual/en/function.date.php)

**Parameters:**

- `$date` (string)
- `$format` (string): Any valid date/time format
- `$strict = 'true'` (bool)

**Returns:**

- (bool)

**Example:**

```
use Bayfront\TimeHelpers\Time;

$date = '2020-07-18';

if (Time::isFormat($date, 'Y-m-d')) {
    // Do something
}
```

<hr />

### inPast

**Description:**

Checks if date/time is in the past.

See: [https://www.php.net/manual/en/datetime.formats.php](https://www.php.net/manual/en/datetime.formats.php)

**Parameters:**

- `$date` (string): Any valid date/time format

**Returns:**

- (bool)

**Example:**

```
use Bayfront\TimeHelpers\Time;

if (Time::inPast('last Tuesday')) {
    // Do something
}
```

<hr />

### inFuture

**Description:**

Checks if date/time is in the future.

See: [https://www.php.net/manual/en/datetime.formats.php](https://www.php.net/manual/en/datetime.formats.php)

**Parameters:**

- `$date` (string): Any valid date/time format

**Returns:**

- (bool)

**Example:**

```
use Bayfront\TimeHelpers\Time;

if (Time::inFuture('2050-12-31')) {
    // Do something
}
```

<hr />

### isBefore

**Description:**

Checks if date/time is before a given date/time.

See: [https://www.php.net/manual/en/datetime.formats.php](https://www.php.net/manual/en/datetime.formats.php)

**Parameters:**

- `$date` (string): Any valid date/time format
- `$before` (string): Any valid date/time format

**Returns:**

- (bool)

**Example:**

```
use Bayfront\TimeHelpers\Time;

if (Time::isBefore('today', '2050-12-31')) {
    // Do something
}
```

<hr />

### isAfter

**Description:**

Checks if date/time is after a given date/time.

See: [https://www.php.net/manual/en/datetime.formats.php](https://www.php.net/manual/en/datetime.formats.php)

**Parameters:**

- `$date` (string): Any valid date/time format
- `$after` (string): Any valid date/time format

**Returns:**

- (bool)

**Example:**

```
use Bayfront\TimeHelpers\Time;

if (Time::isAfter('today', '2050-12-31')) {
    // Do something
}
```

<hr />

### stopwatch

**Description:**

Return the amount of time (in seconds) the callback took to execute.

**Parameters:**

- `$callable` (callback)
- `$times = 1` (int): Number of times to iterate the callback
- `$decimals = 5` (int): Number of decimal places to round to

**Returns:**

- (float)

**Example:**

```
use Bayfront\TimeHelpers\Time;

$elapsed = Time::stopwatch(function() {

    sleep(2);

}, 2);
```