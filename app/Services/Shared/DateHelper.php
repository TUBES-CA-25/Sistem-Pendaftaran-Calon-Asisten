<?php

namespace App\Services\Shared;

class DateHelper
{
    /**
     * Format date from string
     *
     * @param string|null $date Date string
     * @param string $format Date format (default: 'd F Y')
     * @return string Formatted date or '-'
     */
    public static function formatDate($date, $format = 'd F Y')
    {
        if (empty($date)) {
            return '-';
        }

        $timestamp = strtotime($date);
        return $timestamp ? date($format, $timestamp) : '-';
    }

    /**
     * Format time from string
     *
     * @param string|null $time Time string
     * @param string $format Time format (default: 'H:i')
     * @return string Formatted time or '-'
     */
    public static function formatTime($time, $format = 'H:i')
    {
        if (empty($time)) {
            return '-';
        }

        $timestamp = strtotime($time);
        return $timestamp ? date($format, $timestamp) : '-';
    }

    /**
     * Format datetime from string
     *
     * @param string|null $datetime Datetime string
     * @param string $format Datetime format (default: 'd F Y H:i')
     * @return string Formatted datetime or '-'
     */
    public static function formatDateTime($datetime, $format = 'd F Y H:i')
    {
        if (empty($datetime)) {
            return '-';
        }

        $timestamp = strtotime($datetime);
        return $timestamp ? date($format, $timestamp) : '-';
    }
}
