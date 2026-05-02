<?php
declare(strict_types = 1);
namespace Carcosa\Core\I18n;

use \Collator;

/**
 * An abstract base class for locales.
 */
abstract class AbstractLocale implements iLocale
{
    
    /**
     * The country code.
     * @var string
     */
    private string $countryCode;
    
    /**
     * The language code.
     * @var string
     */
    private string $languageCode;
    
    /**
     * The digit grouping character.
     * @var strin
     */
    private string $digitGroupingCharacter;
    
    /**
     * The decimal character.
     * @var string
     */
    private string $decimalCharacter;
    
    /**
     * The long date format.
     * @var string
     */
    private string $longDateFormat;
    
    /**
     * The short date format.
     * @var string
     */
    private string $shortDateFormat;
    
    /**
     * The time format with seconds.
     * @var string
     */
    private string $timeFormatWithSeconds;
    
    /**
     * The time format without seconds.
     * @var string
     */
    private string $timeFormatWithoutSeconds;
    
    /**
     * Whether to prepend currency codes to their amounts.
     * @var bool
     */
    private bool $prependCurrencyCode;
    
    /**
     * construct an instance of this class.
     * @param string $locale The locale code (e.g. "en-US"). This
     * is case-insensitive, and allows hyphens or underscores.
     * @throws \InvalidArgumentException If an invalid locale
     * code is supplied.
     */
    public function __construct(string $locale)
    {
        
        // Normalize the locale code.
        $localeFactory      = \App::make(LocaleFactory::class);
        $normalizedLocale   = $localeFactory->normalizeLocaleCode($locale); 
        
        // Parse the locale code.
        [$languageCode, $countryCode] = explode("-", $normalizedLocale);
        
        // Record the supplied locale.
        $this
            ->setLanguageCode($languageCode)
            ->setcountryCode($countryCode);
        
    }

    /**
     * Set the country code.
     * @param string $countryCode
     * @return $this
     */
    private function setCountryCode(string $countryCode) : self
    {
        $this->countryCode = $countryCode;
        return $this;
    }

    /**
     * Get the country code.
     * @return string
     */
    public function getCountryCode() : string
    {
        return $this->countryCode;
    }
    
    /**
     * Set the language code.
     * @param string $languageCode
     * @return $this
     */
    private function setLanguageCode(string $languageCode) : self
    {
        $this->languageCode = $languageCode;
        return $this;
    }
    
    /**
     * Get the language code.
     * @return string
     */
    public function getLanguageCode() : string
    {
        return $this->languageCode;
    }
    
    /**
     * Set the digit grouping character.
     * @param string $character
     * @return $this
     */
    protected function setDigitGroupingCharacter(string $character) : self
    {
        $this->digitGroupingCharacter = $character;
        return $this;
    }
    
    /**
     * Get the digit grouping character.
     * @return string
     */
    public function getDigitGroupingCharacter() : string
    {
        return $this->digitGroupingCharacter;
    }
    
    /**
     * Set the numeric decimal character.
     * @param string $character
     * @return $this
     */
    protected function setDecimalCharacter(string $character) : self
    {
        $this->decimalCharacter = $character;
        return $this;
    }
    
    /**
     * Get the numeric decimal character.
     * @return string
     */
    public function getDecimalCharacter() : string
    {
        return $this->decimalCharacter;
    }
    
    /**
     * Set the long date format.
     * @param string $format The date format, as used by the PHP
     * date_format() function.
     * @return $this
     */
    protected function setLongDateFormat(string $format) : self
    {
        $this->longDateFormat = $format;
        return $this;
    }
    
    /**
     * Get the long date format.
     * @return string The date format, as used by the PHP
     * date_format() function.
     */
    public function getLongDateFormat() : string
    {
        return $this->longDateFormat;
    }
    
    /**
     * Set the short date format.
     * @param string $format The date format, as used by the PHP
     * date_format() function.
     * @return $this
     */
    protected function setShortDateFormat(string $format) : self
    {
        $this->shortDateFormat = $format;
        return $this;
    }
    
    /**
     * Get the short date format.
     * @return string The date format, as used by the PHP
     * date_format() function.
     */
    public function getShortDateFormat() : string
    {
        return $this->shortDateFormat;
    }
    
    /**
     * Set the time format with seconds.
     * @param string $format The time format, as used by the PHP
     * return $this
     */
    protected function setTimeFormatWithSeconds(string $format) : self
    {
        $this->timeFormatWithSeconds = $format;
        return $this;
    }
    
    /**
     * Set the time format without seconds.
     * @param string $format The time format, as used by the PHP
     * return $this
     */
    protected function setTimeFormatWithoutSeconds(string $format) : self
    {
        $this->timeFormatWithoutSeconds = $format;
        return $this;
    }
    
    /**
     * Get the time format.
     * @return string The time format, as used by the PHP
     * @param bool $withSeconds Whether to include seconds in the time.
     * date_format() function.
     */
    public function getTimeFormat(bool $withSeconds) : string
    {
        return ($withSeconds)
            ? $this->timeFormatWithSeconds
            : $this->timeFormatWithoutSeconds;
    }
    
    /**
     * Get the short date/time format.
     * @param bool $withSeconds Whether to include seconds in the time.
     * date_format() function.
     * @return string The date/time format, as used by the PHP
     * date_format() function.
     */
    public function getShortDateTimeFormat(bool $withSeconds) : string
    {
        return $this->getShortDateFormat() . " " . $this->getTimeFormat($withSeconds);
    }
    
    /**
     * Get the long date/time format.
     * @param bool $withSeconds Whether to include seconds in the time.
     * date_format() function.
     * @return string The date/time format, as used by the PHP
     * date_format() function.
     */
    public function getLongDateTimeFormat(bool $withSeconds) : string
    {
        return $this->getLongDateFormat() . " " . $this->getTimeFormat($withSeconds);
    }
    
    /**
     * Set whether currency codes are prepended to currency amounts.
     * @param bool $prepend If true, currency codes will be prepended to
     * amounts. If false, they will be appended instead.
     */
    protected function setPrependCurrencyCode(bool $prepend) : self
    {
        $this->prependCurrencyCode = $prepend;
        return $this;
    }
    
    
    /**
     * Get whether currency codes are prepended to currency amounts.
     * @return bool If true, currency codes will be prepended to
     * amounts. If false, they will be appended instead.
     */
    public function getPrependCurrencyCode() : bool
    {
        return $this->prependCurrencyCode;
    }
    
    /**
     * Format a human-readable number.
     * @param float $number
     * @param int|null $decimals The number of decimal places to round
     * the number to. Null indicates no rounding.
     * @return string The human-formatted number.
     */
    public function formatNumber(float $number, int|null $decimals = null) : string
    {
        return number_format(
            $number,
            $decimals,
            $this->getDecimalCharacter(),
            $this->getDigitGroupingCharacter()
        );
    }
    
    /**
     * Format a date/time in a long date format.
     * @param DateTimeInterface|null $dateTime
     * @return string 
     */
    public function formatLongDate(\DateTimeInterface|null $dateTime) : string
    {
        return (string) $dateTime?->format($this->getLongDateFormat());
    }
    
    /**
     * Format a date/time in a long date/time format.
     * @param DateTimeInterface|null $dateTime
     * @param bool $withSeconds Whether to include seconds in the output.
     * @return string 
     */
    public function formatLongDateTime(\DateTimeInterface|null $dateTime, bool $withSeconds) : string
    {
        return (string) $dateTime?->format($this->getLongDateTimeFormat($withSeconds));
    }
    
    /**
     * Format a date/time in a long time format.
     * @param DateTimeInterface|null $dateTime
     * @param bool $withSeconds Whether to include seconds in the output.
     * @return string 
     */
    public function formatLongTime(\DateTimeInterface|null $dateTime, bool $withSeconds) : string
    {
        return (string) $dateTime?->format($this->getLongTimeFormat($withSeconds));
    }
    
    /**
     * Format a date/time in a short date format.
     * @param DateTimeInterface|null $dateTime
     * @return string 
     */
    public function formatShortDate(\DateTimeInterface|null $dateTime) : string
    {
        return (string) $dateTime?->format($this->getShortDateFormat());
    }
    
    /**
     * Format a date/time in a short date/time format.
     * @param DateTimeInterface|null $dateTime
     * @param bool $withSeconds Whether to include seconds in the output.
     * @return string 
     */
    public function formatShortDateTime(\DateTimeInterface|null $dateTime, bool $withSeconds) : string
    {
        return (string) $dateTime?->format($this->getShortDateTimeFormat($withSeconds));
    }
    
    /**
     * Format a date/time in a short time format.
     * @param DateTimeInterface|null $dateTime
     * @param bool $withSeconds Whether to include seconds in the output.
     * @return string 
     */
    public function formatShortTime(\DateTimeInterface|null $dateTime, bool $withSeconds) : string
    {
        return (string) $dateTime?->format($this->getShortTimeFormat($withSeconds));
    }
    
    /**
     * Convert this instance to a string locale code.
     * @return string
     */
    public function __toString() : string
    {
        return $this->getLanguageCode() . "-" . $this->getCountryCode();
    }
    
    /**
     * Get a Collator instance for this locale.
     * @return \Collator
     * @throws \RuntimeException If no collator for the current locale
     * can be created.
     */
    public function getCollator() : Collator
    {
        // The collator expects the locale code to be in the
        // format "en_US" (with an underscore delimiter).
        $localeString = implode("_", [
            $this->getLanguageCode(),
            $this->getCountryCode()
        ]);
        
        // Instantiate the collator.
        $collator = Collator::create($localeString);
        if (null === $collator) {
            
            // Collator instantiation failed.
            throw new \RuntimeException(
                "Failed to instantiate a collator in " . __METHOD__ . ": " .
                intl_get_error_message()
            );
            
        }
        
        return $collator;
    }
    
}
