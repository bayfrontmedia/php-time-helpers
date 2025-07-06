<?php

namespace Bayfront\TimeHelpers;

use DateTime;
use DateTimeZone;
use Bayfront\ArrayHelpers\Arr;

class Time
{

    /**
     * Get estimated minutes necessary to read content, based on
     * reading a given amount of words per minute (WPM).
     *
     * @param string $content
     * @param int $wpm
     * @return int (Read time in minutes)
     */
    public static function getReadTime(string $content, int $wpm = 180): int
    {

        $words = str_word_count($content); // Number of words in content

        $minutes = round($words / $wpm); // Minutes required

        if ($minutes < 1) {
            return 1;
        }

        return $minutes;

    }

    /**
     * Returns datetime of a given timestamp, or current time (default).
     *
     * @param int|null $timestamp
     * @return string
     */
    public static function getDateTime(?int $timestamp = NULL): string
    {

        if (NULL === $timestamp) {
            $timestamp = time();
        }

        return date('Y-m-d H:i:s', $timestamp);

    }

    /**
     * Checks if a given year is a leap year using current year by default.
     *
     * @param int|null $year (Four digit year, PHP date('Y') format)
     * @return bool
     */
    public static function isLeapYear(?int $year = NULL): bool
    {

        if (NULL === $year) {
            $year = date('Y');
        }

        return $year % 4 == 0 && ($year % 100 != 0 || $year % 400 == 0);

    }

    /*
     * Required keys for a language array
     */

    private static array $language_keys = [
        'year',
        'years',
        'month',
        'months',
        'week',
        'weeks',
        'day',
        'days',
        'hour',
        'hours',
        'minute',
        'minutes',
        'second',
        'seconds',
        'past',
        'present',
        'future'
    ];

    /*
     * Default language array
     */

    private static array $language = [
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

    /**
     * Returns human time as an array.
     *
     * @param int $time_start (Timestamp of starting time)
     * @param int $time_end (Timestamp of ending time)
     * @param string $limit (Limit of time duration to calculate)
     * @param array|null $language (Custom language to return)
     * @return array
     */
    public static function humanArray(int $time_start, int $time_end, string $limit = 'year', ?array $language = NULL): array
    {

        if (!is_array($language) || (Arr::isMissing($language, self::$language_keys))) {
            $language = self::$language;
        }

        $diff = $time_end - $time_start;

        if ($diff == 0) {
            return ['tense' => $language['present']];
        }

        $types = [
            'year' => 31536000, // 86400 * 365
            'month' => 2592000, // 86400 * 30
            'week' => 604800, // 86400 * 7
            'day' => 86400,
            'hour' => 3600,
            'minute' => 60,
            'second' => 1
        ];

        $return = [];

        $type = $language['future'];

        if ($diff < 0) { // If in the past
            $type = $language['past'];
            $diff = abs($diff);
        }

        $found = false;

        foreach ($types as $k => $v) {

            if ($k != $limit && false === $found && $k != 'second') { // Skip array types above the given $limit
                continue;
            }

            $found = true;

            $total = floor($diff / $v);

            if ($total != 1) {
                $k = $language[$k . 's']; // Get plural translation
            } else {
                $k = $language[$k]; // Singular translation
            }

            $return[$k] = $total;

            $diff = $diff - ($return[$k] * $v); // Calculate remaining seconds

        }

        $return['tense'] = $type;

        return $return;

    }

    /**
     * Returns human time as a string.
     *
     * @param int $time_start (Timestamp of starting time)
     * @param int $time_end (Timestamp of ending time)
     * @param string $limit (Limit of time duration to calculate)
     * @param array|null $language (Custom language to return)
     * @return string
     */
    public static function human(int $time_start, int $time_end, string $limit = 'year', ?array $language = NULL): string
    {

        $arr = self::humanArray($time_start, $time_end, $limit, $language);

        $return = '';

        foreach ($arr as $k => $v) {

            if (is_numeric($v) && $v == 0) { // Skip zero values
                continue;
            } else if (!is_numeric($v)) {
                $return .= $v . ' ';
            } else {
                $return .= $v . ' ' . $k . ' ';
            }

        }

        return rtrim($return);

    }

    /**
     * Convert UTC datetime to ISO-8601 format.
     *
     * @param int|string $datetime (Any valid date/time formated string or timestamp)
     * @return string
     */
    public static function toIso8601(int|string $datetime): string
    {
        return self::toTimezone($datetime, 'UTC', 'Y-m-d\TH:i:sp');
    }

    /**
     * Convert UTC datetime to format using timezone.
     *
     * See: https://www.php.net/manual/en/timezones.php
     *
     * @param int|string $datetime (Any valid date/time formated string or timestamp)
     * @param string $timezone (Any valid timezone identifier)
     * @param string $format (Any valid date/time format)
     * @return string
     */
    public static function toTimezone(int|string $datetime, string $timezone, string $format = 'U'): string
    {

        $dt = new DateTime();
        $dt->setTimezone(new DateTimeZone($timezone));

        if (is_string($datetime)) {
            $datetime = strtotime($datetime);
        }

        $dt->setTimestamp($datetime);
        return $dt->format($format);

    }

    /**
     * Checks if string is a valid timezone identifier.
     *
     * See: https://www.php.net/manual/en/timezones.php
     *
     * @param string $timezone
     * @return bool
     */
    public static function isTimezone(string $timezone): bool
    {
        return in_array($timezone, DateTimeZone::listIdentifiers());
    }

    /**
     * Checks if value is a given dateTime format.
     *
     * See: https://www.php.net/manual/en/function.date.php
     *
     * @param string $date
     * @param string $format (Any valid date/time format)
     * @param bool $strict
     * @return bool
     */
    public static function isFormat(string $date, string $format, bool $strict = true): bool
    {
        $dateTime = DateTime::createFromFormat($format, $date);

        if ($strict) {

            $errors = DateTime::getLastErrors();

            if (!empty($errors['warning_count'])) {
                return false;
            }

        }

        return $dateTime !== false;

    }

    /**
     * Checks if date/time is in the past.
     *
     * See:  https://www.php.net/manual/en/datetime.formats.php
     *
     * @param string $date (Any valid date/time format)
     * @return bool
     */
    public static function inPast(string $date): bool
    {
        return strtotime($date) < time();
    }

    /**
     * Checks if date/time is in the future.
     *
     * See: https://www.php.net/manual/en/datetime.formats.php
     *
     * @param string $date (Any valid date/time format)
     * @return bool
     */
    public static function inFuture(string $date): bool
    {
        return strtotime($date) > time();
    }

    /**
     * Checks if date/time is before a given date/time.
     *
     * See: https://www.php.net/manual/en/datetime.formats.php
     *
     * @param string $date (Any valid date/time format)
     * @param string $before (Any valid date/time format)
     * @return bool
     */
    public static function isBefore(string $date, string $before): bool
    {
        return strtotime($date) < strtotime($before);
    }

    /**
     * Checks if date/time is after a given date/time.
     *
     * See: https://www.php.net/manual/en/datetime.formats.php
     *
     * @param string $date (Any valid date/time format)
     * @param string $after (Any valid date/time format)
     * @return bool
     */
    public static function isAfter(string $date, string $after): bool
    {
        return strtotime($date) > strtotime($after);
    }

    /**
     * Return the amount of time (in seconds) the callback took to execute.
     *
     * @param callable $callback
     * @param int $times (Number of times to iterate the callback)
     * @param int $decimals (Number of decimal places to round to)
     * @return float
     */
    public static function stopwatch(callable $callback, int $times = 1, int $decimals = 5): float
    {

        $start = microtime(true);

        $i = 0;

        while ($i < $times) {
            $i++;
            $callback();
        }

        $end = microtime(true);

        return round($end - $start, $decimals);

    }

    /**
     * Is date a weekday?
     *
     * @param string $date (Any valid date/time format))
     * @return bool
     */
    public static function isWeekday(string $date): bool
    {
       $day = date('N', strtotime($date));
       return $day >= 1 && $day <= 5;
    }

    /**
     * Is date a weekend?
     *
     * @param string $date (Any valid date/time format)
     * @return bool
     */
    public static function isWeekend(string $date): bool
    {
        return !self::isWeekday($date);
    }

    /**
     * Get random date between two dates.
     *
     * @param string $start_date (Any valid date/time format)
     * @param string|null $end_date (Any valid date/time format. If null, the current date will be used)
     * @param string $format (Date format to be returned)
     * @return string
     */
    public static function getRandomDate(string $start_date = '1900-01-01', ?string $end_date = null, string $format = 'Y-m-d H:i:s'): string
    {
        $start_date = strtotime($start_date);

        if ($end_date === null) {
            $end_date = time();
        } else {
            $end_date = strtotime($end_date);
        }

        if ($start_date > $end_date) { // Swap if out of order
            [$start_date, $end_date] = [$end_date, $start_date];
        }

        $timestamp = mt_rand($start_date, $end_date);
        return date($format, $timestamp);
    }

    private static array $days = [
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
        7 => 'Sunday'
    ];

    /**
     * Get date of the last/next occurring day from a given day and date.
     *
     * @param string $modifier (last/next)
     * @param int $day (Numeric day)
     * @param string $date (Any valid date/time format)
     * @param string $format (Date format to be returned)
     * @param bool $include_self (If true, the current date will be returned if it falls on the day provided)
     * @return string
     */
    private static function getDay(string $modifier, int $day, string $date, string $format = 'Y-m-d H:i:s', bool $include_self = true): string
    {
        $date = new DateTime($date);

        if ($include_self === true && $date->format('N') == $day) {
            return $date->format($format);
        }

        $date->modify($modifier . ' ' . self::$days[$day]);
        return $date->format($format);

    }

    private const DAY_LAST = 'last';
    private const DAY_NEXT = 'next';

    /**
     * Get date of the previous occurring Monday from a given date.
     *
     * @param string $date (Any valid date/time format)
     * @param string $format (Date format to be returned)
     * @param bool $include_self (If true, the current date will be returned if it falls on a Monday)
     * @return string
     */
    public static function lastMonday(string $date, string $format = 'Y-m-d H:i:s', bool $include_self = true): string
    {
        return self::getDay(self::DAY_LAST, 1, $date, $format, $include_self);
    }

    /**
     * Get date of the previous occurring Tuesday from a given date.
     *
     * @param string $date (Any valid date/time format)
     * @param string $format (Date format to be returned)
     * @param bool $include_self (If true, the current date will be returned if it falls on a Tuesday)
     * @return string
     */
    public static function lastTuesday(string $date, string $format = 'Y-m-d H:i:s', bool $include_self = true): string
    {
        return self::getDay(self::DAY_LAST, 2, $date, $format, $include_self);
    }

    /**
     * Get date of the previous occurring Wednesday from a given date.
     *
     * @param string $date (Any valid date/time format)
     * @param string $format (Date format to be returned)
     * @param bool $include_self (If true, the current date will be returned if it falls on a Wednesday)
     * @return string
     */
    public static function lastWednesday(string $date, string $format = 'Y-m-d H:i:s', bool $include_self = true): string
    {
        return self::getDay(self::DAY_LAST, 3, $date, $format, $include_self);
    }

    /**
     * Get date of the previous occurring Thursday from a given date.
     *
     * @param string $date (Any valid date/time format)
     * @param string $format (Date format to be returned)
     * @param bool $include_self (If true, the current date will be returned if it falls on a Thursday)
     * @return string
     */
    public static function lastThursday(string $date, string $format = 'Y-m-d H:i:s', bool $include_self = true): string
    {
        return self::getDay(self::DAY_LAST, 4, $date, $format, $include_self);
    }

    /**
     * Get date of the previous occurring Friday from a given date.
     *
     * @param string $date (Any valid date/time format)
     * @param string $format (Date format to be returned)
     * @param bool $include_self (If true, the current date will be returned if it falls on a Friday)
     * @return string
     */
    public static function lastFriday(string $date, string $format = 'Y-m-d H:i:s', bool $include_self = true): string
    {
        return self::getDay(self::DAY_LAST, 5, $date, $format, $include_self);
    }

    /**
     * Get date of the previous occurring Saturday from a given date.
     *
     * @param string $date (Any valid date/time format)
     * @param string $format (Date format to be returned)
     * @param bool $include_self (If true, the current date will be returned if it falls on a Saturday)
     * @return string
     */
    public static function lastSaturday(string $date, string $format = 'Y-m-d H:i:s', bool $include_self = true): string
    {
        return self::getDay(self::DAY_LAST, 6, $date, $format, $include_self);
    }

    /**
     * Get date of the previous occurring Sunday from a given date.
     *
     * @param string $date (Any valid date/time format)
     * @param string $format (Date format to be returned)
     * @param bool $include_self (If true, the current date will be returned if it falls on a Sunday)
     * @return string
     */
    public static function lastSunday(string $date, string $format = 'Y-m-d H:i:s', bool $include_self = true): string
    {
        return self::getDay(self::DAY_LAST, 7, $date, $format, $include_self);
    }
    /**
     * Get date of the next occurring Monday from a given date.
     *
     * @param string $date (Any valid date/time format)
     * @param string $format (Date format to be returned)
     * @param bool $include_self (If true, the current date will be returned if it falls on a Monday)
     * @return string
     */
    public static function nextMonday(string $date, string $format = 'Y-m-d H:i:s', bool $include_self = true): string
    {
        return self::getDay(self::DAY_NEXT, 1, $date, $format, $include_self);
    }

    /**
     * Get date of the next occurring Tuesday from a given date.
     *
     * @param string $date (Any valid date/time format)
     * @param string $format (Date format to be returned)
     * @param bool $include_self (If true, the current date will be returned if it falls on a Tuesday)
     * @return string
     */
    public static function nextTuesday(string $date, string $format = 'Y-m-d H:i:s', bool $include_self = true): string
    {
        return self::getDay(self::DAY_NEXT, 2, $date, $format, $include_self);
    }

    /**
     * Get date of the next occurring Wednesday from a given date.
     *
     * @param string $date (Any valid date/time format)
     * @param string $format (Date format to be returned)
     * @param bool $include_self (If true, the current date will be returned if it falls on a Wednesday)
     * @return string
     */
    public static function nextWednesday(string $date, string $format = 'Y-m-d H:i:s', bool $include_self = true): string
    {
        return self::getDay(self::DAY_NEXT, 3, $date, $format, $include_self);
    }

    /**
     * Get date of the next occurring Thursday from a given date.
     *
     * @param string $date (Any valid date/time format)
     * @param string $format (Date format to be returned)
     * @param bool $include_self (If true, the current date will be returned if it falls on a Thursday)
     * @return string
     */
    public static function nextThursday(string $date, string $format = 'Y-m-d H:i:s', bool $include_self = true): string
    {
        return self::getDay(self::DAY_NEXT, 4, $date, $format, $include_self);
    }

    /**
     * Get date of the next occurring Friday from a given date.
     *
     * @param string $date (Any valid date/time format)
     * @param string $format (Date format to be returned)
     * @param bool $include_self (If true, the current date will be returned if it falls on a Friday)
     * @return string
     */
    public static function nextFriday(string $date, string $format = 'Y-m-d H:i:s', bool $include_self = true): string
    {
        return self::getDay(self::DAY_NEXT, 5, $date, $format, $include_self);
    }

    /**
     * Get date of the next occurring Saturday from a given date.
     *
     * @param string $date (Any valid date/time format)
     * @param string $format (Date format to be returned)
     * @param bool $include_self (If true, the current date will be returned if it falls on a Saturday)
     * @return string
     */
    public static function nextSaturday(string $date, string $format = 'Y-m-d H:i:s', bool $include_self = true): string
    {
        return self::getDay(self::DAY_NEXT, 6, $date, $format, $include_self);
    }

    /**
     * Get date of the next occurring Sunday from a given date.
     *
     * @param string $date (Any valid date/time format)
     * @param string $format (Date format to be returned)
     * @param bool $include_self (If true, the current date will be returned if it falls on a Sunday)
     * @return string
     */
    public static function nextSunday(string $date, string $format = 'Y-m-d H:i:s', bool $include_self = true): string
    {
        return self::getDay(self::DAY_NEXT, 7, $date, $format, $include_self);
    }

}