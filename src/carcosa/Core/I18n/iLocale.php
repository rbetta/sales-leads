<?php
declare(strict_types = 1);
namespace Carcosa\Core\I18n;

use \Collator;

/**
 * An interface that represents a locale.
 */
interface iLocale extends \Stringable
{
    
    /**
     * Get the country code.
     * @return string
     */
    public function getCountryCode() : string;
    
    /**
     * Get the language code.
     * @return string
     */
    public function getLanguageCode() : string;
    
    /**
     * Get the digit grouping character.
     * @return string
     */
    public function getDigitGroupingCharacter() : string;
    
    /**
     * Get the numeric decimal character.
     * @return string
     */
    public function getDecimalCharacter() : string;
    
    /**
     * Get the long date format.
     * @return string The date format, as used by the PHP
     * date_format() function.
     */
    public function getLongDateFormat() : string;
    
    /**
     * Get the short date format.
     * @return string The date format, as used by the PHP
     * date_format() function.
     */
    public function getShortDateFormat() : string;
    
    /**
     * Get the time format.
     * @return string The date format, as used by the PHP
     * @param bool $withSeconds Whether to include seconds in the time.
     * date_format() function.
     */
    public function getTimeFormat(bool $withSeconds) : string;
    
    /**
     * Get the short date/time format.
     * @param bool $withSeconds Whether to include seconds in the time.
     * date_format() function.
     * @return string The date/time format, as used by the PHP
     * date_format() function.
     */
    public function getShortDateTimeFormat(bool $withSeconds) : string;
    
    /**
     * Get the long date/time format.
     * @param bool $withSeconds Whether to include seconds in the time.
     * date_format() function.
     * @return string The date/time format, as used by the PHP
     * date_format() function.
     */
    public function getLongDateTimeFormat(bool $withSeconds) : string;
    
    /**
     * Get whether currency codes are prepended to currency amounts.
     * @return bool If true, currency codes will be prepended to
     * amounts. If false, they will be appended instead.
     */
    public function getPrependCurrencyCode() : bool;
    
    /**
     * Format a human-readable number.
     * @param float $number
     * @param int|null $decimals The number of decimal places to round
     * the number to. Null indicates no rounding.
     * @return string The human-formatted number.
     */
    public function formatNumber(float $number, int|null $decimals = null) : string;
    
    /**
     * Format a date/time in a long date format.
     * @param DateTimeInterface|null $dateTime
     * @return string 
     */
    public function formatLongDate(\DateTimeInterface|null $dateTime) : string;
    
    /**
     * Format a date/time in a long date/time format.
     * @param DateTimeInterface|null $dateTime
     * @param bool $withSeconds Whether to include seconds in the output.
     * @return string 
     */
    public function formatLongDateTime(\DateTimeInterface|null $dateTime, bool $withSeconds) : string;
    
    /**
     * Format a date/time in a long time format.
     * @param DateTimeInterface|null $dateTime
     * @param bool $withSeconds Whether to include seconds in the output.
     * @return string 
     */
    public function formatLongTime(\DateTimeInterface|null $dateTime, bool $withSeconds) : string;
    
    /**
     * Format a date/time in a short date format.
     * @param DateTimeInterface|null $dateTime
     * @return string 
     */
    public function formatShortDate(\DateTimeInterface|null $dateTime) : string;
    
    /**
     * Format a date/time in a short date/time format.
     * @param DateTimeInterface|null $dateTime
     * @param bool $withSeconds Whether to include seconds in the output.
     * @return string 
     */
    public function formatShortDateTime(\DateTimeInterface|null $dateTime, bool $withSeconds) : string;
    
    /**
     * Format a date/time in a short time format.
     * @param DateTimeInterface|null $dateTime
     * @param bool $withSeconds Whether to include seconds in the output.
     * @return string 
     */
    public function formatShortTime(\DateTimeInterface|null $dateTime, bool $withSeconds) : string;
    
    /**
     * Get a Collator instance for this locale.
     * @return \Collator
     */
    public function getCollator() : Collator;
    
}
