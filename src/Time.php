<?php
/**
 * Helper class to provide useful time related functions
 *
 * @version     1.0.0
 * @link        https://github.com/bayfrontmedia/php-time-helpers
 * @license     MIT https://opensource.org/licenses/MIT
 * @copyright   2020 Bayfront Media https://www.bayfrontmedia.com
 * @author      John Robinson <john@bayfrontmedia.com>
 */

namespace Bayfront\TimeHelpers;

use Bayfront\ArrayHelpers\Arr;

class Time
{

    /**
     * Get estimated minutes necessary to read content, based on
     * reading a given amount of words per minute (WPM)
     *
     * @param $content string
     * @param $wpm int
     *
     * @return int (Read time in minutes)
     */

    public static function getReadTime(string $content, int $wpm = 180): int
    {

        $words = str_word_count($content, 0); // Number of words in content

        $minutes = round($words / $wpm); // Minutes required

        if ($minutes < 1) {

            return 1;

        }

        return $minutes;

    }

    /**
     * Returns datetime of a given timestamp, or current time (default)
     *
     * @param $timestamp int
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
     * Checks if a given year is a leap year using current year by default
     *
     * @param $year int (Four digit year, PHP date('Y') format)
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
     * Returns human time as an array
     *
     * @param int $time_start (Timestamp of starting time)
     * @param int $time_end (Timestamp of ending time)
     * @param string $limit (Limit of time duration to calculate)
     * @param array $language (Custom language to return)
     *
     * @return array
     */

    public static function humanArray(int $time_start = NULL, int $time_end = NULL, string $limit = 'year', array $language = NULL): array
    {

        if (NULL === $time_start) {
            $time_start = time();
        }

        if (NULL === $time_end) {
            $time_end = time();
        }

        if (!is_array($language) || (Arr::isMissing($language, self::$language_keys))) {
            $language = self::$language;
        }

        $diff = $time_end - $time_start;

        if ($diff == 0) {
            return ['tense' => $language['just_now']];
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
     * Returns human time as a string
     *
     * @param int $time_start (Timestamp of starting time)
     * @param int $time_end (Timestamp of ending time)
     * @param string $limit (Limit of time duration to calculate)
     * @param array $language (Custom language to return)
     *
     * @return string
     */

    public static function human(int $time_start = NULL, int $time_end = NULL, string $limit = 'year', array $language = NULL): string
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

}