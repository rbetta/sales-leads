<?php
declare(strict_types = 1);
namespace Carcosa\Core\I18n\Locales;

use Carcosa\Core\I18n\AbstractLocale;

/**
 * A class that represents the en-US locale.
 */
class LocaleEnUs extends AbstractLocale
{
    
    /**
     * construct an instance of this class.
     */
    public function __construct()
    {
        
        // Record the locale code.
        parent::__construct("en-US");
        
        // Set locale-specific data.
        $this
            ->SetDigitGroupingCharacter(",")
            ->setDecimalCharacter(".")
            ->setShortDateFormat("n/j/Y")
            ->setLongDateFormat("M j, Y")
            ->setTimeFormatWithSeconds("h:i:s A")
            ->setTimeFormatWithoutSeconds("h:i A")
            ->setPrependCurrencyCode(false);
        
    }

}
