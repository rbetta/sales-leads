<?php
declare(strict_types = 1);
namespace Carcosa\Core\I18n;

use Carcosa\Core\I18n\Locales\LocaleEnUs;
use Carcosa\Core\Regex\RegexFactory;

/**
 * A factory for instantiating locale classes that implement the
 * iLocale interface.
 */
class LocaleFactory
{
    
    /**
     * Get the default locale.
     * @return iLocale
     * @todo This should have more dynamic logic (for example, by domain name/TLD or browser language headers).
     */
    public function getDefaultLocale() : iLocale
    {
        return \App::make(LocaleEnUs::class); 
    }
    
    /**
     * Create a locale by its code.
     * @param string $localeCode The locale code (e.g. "en-US").
     * This is case-insensitive, and allows hyphens or underscores.
     * @return iLocale
     * @throws \InvalidArgumentException If an invalid locale
     * code is supplied.
     */
    public function create(string $localeCode) : iLocale
    {
        
        // Normalize the supplied locale.
        $normalizedLocaleCode = $this->normalizeLocaleCode($localeCode);
        
        // Instantiate the correct locale.
        $locales = $this->getAllLocaleCodes();
        if (in_array($normalizedLocaleCode, $locales, true)) {
            
            // This is a known locale code. Parse it to determine the
            // correct class to instantiate.
            [$languageCode, $countryCode] = explode("-", $normalizedLocaleCode);
            
            // Determine the name of the class to instantiate.
            $className = implode("", [
                "Locale",
                ucwords(strtolower($languageCode)),
                ucwords(strtolower($countryCode)),
            ]); 
            
            // Calculate the fully qualified name of the class.
            $namespacedClass = implode("\\", [
                __NAMESPACE__,
                "Locales",
                $className,
            ]);
            
            // Note that the foregoing code ensures the calculated value is
            // created solely by concatenating values from a whitelist, and
            // is therefore guaranteed to be safe.
            //
            // However, for extra security against future bugs being
            // introduced, we will strip null characters from the generated
            // class name. This is a precaution against null byte attacks
            // directed at filesystem functions, since underlying C code
            // can interpret these injected characters as end-of-string
            // markers (and unexpected path truncation can thus occur in
            // vulnerable libraries).
            $namespacedClass = str_replace("\0", "", $namespacedClass);
            
            // Return a new instance of the correct class.
            return \App::make($namespacedClass);
            
        }
        
        // An unknown locale was supplied.
        throw new \InvalidArgumentException(
            "The invalid locale code \"$localeCode\" was supplied to " .
            __METHOD__
        );
        
    }
    
    /**
     * Get all known locale codes.
     * @return string[] An array of locale codes (in the format "en-US").
     */
    public function getAllLocaleCodes() : array
    {
        return [
            'en-US',
        ];
    }
    
    /**
     * Normalize a locale code.
     * @param string $locale The locale code (e.g. "en-US"). This
     * is case-insensitive, and allows hyphens or underscores.
     * @return string The supplied locale code in the format "en-US"
     * (including the hyphen and this exact capitalization).
     * @throws \InvalidArgumentException If the supplied locale code
     * is not in a variation of the allowed format.
     */
    public function normalizeLocaleCode(string $locale) : string
    {
        
        // Normalize the supplied locale.
        $normalizedLocale = str_replace('_', '-', $locale);
        
        // Validate the locale.
        $regexFactory = \App::make(RegexFactory::class);
        $regex = $regexFactory->create('/^[a-z]{2}-[a-z]{2}$/i');
        if (! $regex->getIsMatch($normalizedLocale)) {
            throw new \InvalidArgumentException(
                "The invalid locale code \"$normalizedLocale\" " .
                "was supplied to " . __METHOD__ . " (expected: " .
                "case-insensitive value in the example " .
                "format \"en-US\" or \"en_US\")"
            );
        }
        
        // Parse the locale code into its component parts.
        [$languageCode, $countryCode] = explode("-", $normalizedLocale);
        
        // Return a reconstructed locale code with the expected
        // capitalization.
        return strtolower($languageCode) . "-" . strtoupper($countryCode);
        
    }
    
}
