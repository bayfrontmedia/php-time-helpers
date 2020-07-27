## PHP time helpers

Helper class to provide useful time related functions.

- [License](#license)
- [Author](#author)
- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)

## License

This project is open source and available under the [MIT License](https://github.com/bayfrontmedia/php-array-helpers/blob/master/LICENSE).

## Author

John Robinson, [Bayfront Media](https://www.bayfrontmedia.com)

## Requirements

* PHP > 7.1.0

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

- `$timestamp` (int)

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

- `$year` (int): Four digit year, PHP `date('Y')` format

**Returns:**

- (string)

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
- `$language` (array): Custom language to return

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
- `$language` (array): Custom language to return

**Returns:**

- (string)

**Example:**

```
use Bayfront\TimeHelpers\Time;

$start = time();
$end = time() + 51001;

echo Time::human($start, $end);
```