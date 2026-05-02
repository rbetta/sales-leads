<?php
declare(strict_types=1);
namespace App\Models\DataObjects\User;

use Carbon\CarbonInterface;
use Carcosa\Core\DataObjects\AbstractSoftDeletableTimestampedUuid;
use Carcosa\Core\I18n\iLocale;
use Carcosa\Core\Json\Adapters\CarbonJsonAdapter;
use Illuminate\Contracts\Validation\Validator;

/**
 * A class that represents a user account.
 */
class UserData extends AbstractSoftDeletableTimestampedUuid implements iUserData
{
    
    /**
     * Set the client ID.
     * @param string $clientId
     * @return $this
     */
    public function setClientId(string $clientId) : self
    {
        return $this->setProperty("clientId", $clientId);
    }
    
    /**
     * Get the client ID.
     * @return string
     */
    public function getClientId() : string
    {
        return $this->getProperty("clientId");
    }
    
    /**
     * Set the username.
     * @param string $username
     * @return $this
     */
    public function setUsername(string $username) : self
    {
        return $this->setProperty("username", $username);
    }
    
    /**
     * Get the username.
     * @return string
     */
    public function getUsername() : string
    {
        return $this->getProperty("username");
    }
    
    /**
     * Get the email address.
     * @param string $email
     * @return $this
     */
    public function setEmail(string $email) : self
    {
        return $this->setProperty("email", $email);
    }
    
    /**
     * Get the email address.
     * @return string
     */
    public function getEmail() : string
    {
        return $this->getProperty("email");
    }

    /**
     * Set the human-readable label.
     * @param string $label
     * @return $this
     */
    public function setLabel(string $label) : self
    {
        return $this->setProperty("label", $label);
    }
    
    /**
     * Get the human-readable label.
     * @return string
     */
    public function getLabel() : string
    {
        return $this->getProperty("label");
    }
    
    /**
     * Set the locale for the user account.
     * @param iLocale $locale
     * @return $this
     */
    public function setLocale(iLocale $locale) : self
    {
        return $this->setProperty('locale', $locale);
    }
    
    /**
     * Get the locale for the user account.
     * @return iLocale
     */
    public function getLocale() : iLocale
    {
        return $this->getProperty('locale');
    }
    
    /**
     * Set the timezone for the user account.
     * @param \DateTimeZone $timezone
     * @return $this
     */
    public function setTimezone(\DateTimeZone $timezone) : self
    {
        return $this->setProperty('timezone', $timezone);
    }
    
    /**
     * Get the timezone for the user account.
     * @return \DateTimeZone
     */
    public function getTimezone() : \DateTimeZone
    {
        return $this->getProperty('timezone');
    }
    
    /**
     * Set whether the user is a seller.
     * @param bool $isSeller
     * @return $this
     */
    public function setIsSeller(bool $isSeller) : self
    {
        return $this->setProperty('isSeller', $isSeller);
    }
    
    /**
     * Get whether the user is a seller.
     * @return bool
     */
    public function getIsSeller() : bool
    {
        return $this->getProperty('isSeller');
    }
    
    /**
     * Set whether the user is a customer.
     * @param bool $isCustomer
     * @return $this
     */
    public function setIsCustomer(bool $isCustomer) : self
    {
        return $this->setProperty('isCustomer', $isCustomer);
    }
    
    /**
     * Get whether the user is a customer.
     * @return bool
     */
    public function getIsCustomer() : bool
    {
        return $this->getProperty('isCustomer');
    }
    
    /**
     * Set whether the user is a system admin.
     * @param bool $isSystemAdmin
     * @return $this
     */
    public function setIsSystemAdmin(bool $isSystemAdmin) : self
    {
        return $this->setProperty('isSystemAdmin', $isSystemAdmin);
    }
    
    /**
     * Get whether the user is a system admin.
     * @return bool
     */
    public function getIsSystemAdmin() : bool
    {
        return $this->getProperty('isSystemAdmin');
    }
    
    /**
     * Set whether the user account is active.
     * @param bool $isActive
     * @return $this
     */
    public function setIsActive(bool $isActive) : self
    {
        return $this->setProperty("isActive", $isActive);
    }
    
    /**
     * Get whether the user account is active.
     * @return bool
     */
    public function getIsActive() : bool
    {
        return $this->getProperty("isActive");
    }
    
    /**
     * Set the deactivation timestamp, if any.
     * @param ?CarbonInterface $timestamp
     * @return $this
     */
    public function setDeactivatedAt(?CarbonInterface $timestamp) : self
    {
        return $this->setProperty("deactivatedAt", $timestamp);
    }
    
    /**
     * Get the deactivation timestamp, if any.
     * @return ?CarbonInterface
     */
    public function getDeactivatedAt() : ?CarbonInterface
    {
        return $this->getProperty("deactivatedAt");
    }
    
}
