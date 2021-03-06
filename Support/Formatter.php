<?php

namespace Gracious\Interconnect\Support;

use DateTime;

/**
 * Class Formatter
 * @package Gracious\Interconnect\Support
 * Utility class for formatting data and strings to desired format before sending it to the server
 */
abstract class Formatter
{
    /**
     * @param int|string $ID
     * @param string $entityTypeHandle
     * @param string $merchantHandle
     * @return string
     */
    public static function prefixID($ID, $entityTypeHandle, $merchantHandle)
    {
        $entityTypeHandle = preg_replace('/_/', '-', $entityTypeHandle);

        return strtoupper($merchantHandle) . '-' . strtoupper($entityTypeHandle) . '-' . (string)$ID;
    }

    /**
     * Formats a date string to ISO8601 format
     * @param string $dateString
     * @return string
     */
    public static function formatDateStringToIso8601($dateString)
    {
        if (null === $dateString) {
            return null;
        }

        $dateTime = new DateTime($dateString);

        return $dateTime->format(DateTime::ATOM);
    }

    /**
     * @param string $lastName
     * @param string $prefix
     * @return string
     */
    public static function prefixLastName($lastName, $prefix)
    {
        if (is_string($prefix) && '' != trim($prefix)) {
            return $prefix . ' ' . $lastName;
        }

        return $lastName;
    }
}