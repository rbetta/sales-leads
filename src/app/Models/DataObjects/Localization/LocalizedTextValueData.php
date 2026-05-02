<?php
declare(strict_types=1);
namespace Carcosa\Core\DataObjects\Localization;

use Carcosa\Core\DataObjects\AbstractSoftDeletableTimestampedUuid;
use Carcosa\Core\Json\Adapters\CarbonJsonAdapter;
use Illuminate\Contracts\Validation\Validator;

/**
 * A class that represents localized text for a specific locale.
 */
class LocalizedTextValueData extends AbstractSoftDeletableTimestampedUuid
{
    
    /**
     * Set the localized text ID.
     * @param string $localizedTextId The foreign key to the LocalizedTextData
     * record that points to all localized versions of this text.
     * @return $this
     */
    public function setLocalizedTextId(string $localizedTextId) : self
    {
        return $this->setProperty('localizedTextId', $localizedTextId);
    }
    
    /**
     * Get the localized text ID.
     * @return string The foreign key to the LocalizedTextData
     * record that points to all localized versions of this text.
     */
    public function getLocalizedTextId() : string
    {
        return $this->getProperty('localizedTextId');
    }
    
    /**
     * Set the locale.
     * @param string $locale.
     * @return $this
     * @throws \RuntimeException an empty string is supplied.
     */
    public function setLocale(string $locale) : self
    {
        if ("" === $locale) {
            throw new \RuntimeException(
                "An empty string was supplied as a locale to " . __METHOD__
            );
        }
        return $this->setProperty('locale', $locale);
    }
    
    /**
     * Get the locale.
     * @return string
     */
    public function getLocale(string $locale) : self
    {
        return $this->getProperty('locale');
    }
    
    /**
     * Set the localized text value.
     * @param string $value
     * @return $this
     */
    public function setValue(string $value) : self
    {
        return $this->setProperty('value', $value);
    }
    
    /**
     * Get the localized text value.
     * @return string
     */
    public function getValue() : string
    {
        return $this->getProperty('value');
    }
    
}
