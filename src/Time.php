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
     *
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
     *
     * @return string
     */

    public static function getDateTime(int $timestamp = NULL): string
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
     *
     * @return bool
     */

    public static function isLeapYear(int $year = NULL): bool
    {

        if (NULL === $year) {
            $year = date('Y');
        }

        return $year % 4 == 0 && ($year % 100 != 0 || $year % 400 == 0);

    }

    /*
     * Required keys for a language array
     */

    private static $language_keys = [
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

    private static $language = [
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
     *
     * @return array
     */

    public static function humanArray(int $time_start, int $time_end, string $limit = 'year', array $language = NULL): array
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
     *
     * @return string
     */

    public static function human(int $time_start, int $time_end, string $limit = 'year', array $language = NULL): string
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
     * Checks if string is a valid timezone identifier.
     *
     * See: https://www.php.net/manual/en/timezones.php
     *
     * @param string $timezone
     *
     * @return bool
     */

    public static function isTimezone(string $timezone): bool
    {
        return in_array($timezone, DateTimeZone::listIdentifiers());
    }

    /**
     * Checks if value is a given dateTime format.
     *
     * @param string $date
     * @param string $format (Any valid date/time format)
     * @param bool $strict
     *
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
     * @param string $date (Any valid date/time format)
     *
     * @return bool
     */

    public static function inPast(string $date): bool
    {
        return strtotime($date) < time();
    }

    /**
     * Checks if date/time is in the future.
     *
     * @param string $date (Any valid date/time format)
     *
     * @return bool
     */

    public static function inFuture(string $date): bool
    {
        return strtotime($date) > time();
    }

    /**
     * Checks if date/time is before a given date/time.
     *
     * @param string $date (Any valid date/time format)
     * @param string $before (Any valid date/time format)
     *
     * @return bool
     */

    public static function isBefore(string $date, string $before): bool
    {
        return strtotime($date) < strtotime($before);
    }

    /**
     * Checks if date/time is after a given date/time.
     *
     * @param string $date (Any valid date/time format)
     * @param string $after (Any valid date/time format)
     *
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
     *
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

}