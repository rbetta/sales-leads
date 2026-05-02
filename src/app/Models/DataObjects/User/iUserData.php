<?php
declare(strict_types=1);
namespace App\Models\DataObjects\User;

use Carbon\CarbonInterface;
use Carcosa\Core\DataObjects\iSoftDeletableTimestampedUuid;
use Carcosa\Core\I18n\iLocale;

/**
 * An interface for representing a user account.
 */
interface iUserData extends iSoftDeletableTimestampedUuid
{

    /**
     * Set the user's associated client ID.
     * @param string $clientId
     * @return $this
     */
    public function setClientId(string $clientId) : self;
    
    /**
     * Get the user's associated client ID.
     * @return string
     */
    public function getClientId() : string;
    
    /**
     * Set the username.
     * @param string $username
     * @return $this
     */
    public function setUsername(string $username) : self;
    
    /**
     * Get the username.
     * @return string
     */
    public function getUsername() : string;
    
    /**
     * Set the email address.
     * @param string $email
     * @return $this
     */
    public function setEmail(string $email) : self;
    
    /**
     * Get the email address.
     * @return string
     */
    public function getEmail() : string;

    /**
     * Set the human-readable label.
     * @param string $label
     * @return $this
     */
    public function setLabel(string $label) : self;
    
    /**
     * Get the human-readable label.
     * @return string
     */
    public function getLabel() : string;
    
    /**
     * Set the locale for the user account.
     * @param iLocale $locale
     * @return $this
     */
    public function setLocale(iLocale $locale) : self;
    
    /**
     * Get the locale for the user account.
     * @return iLocale
     */
    public function getLocale() : iLocale;
    
    /**
     * Set the timezone for the user account.
     * @param \DateTimeZone $timezone
     * @return $this
     */
    public function setTimezone(\DateTimeZone $timezone) : self;
    
    /**
     * Get the timezone for the user account.
     * @return \DateTimeZone
     */
    public function getTimezone() : \DateTimeZone;
    
    /**
     * Set whether the user is a seller.
     * @param bool $isSeller
     * @return $this
     */
    public function setIsSeller(bool $isSeller) : self;
    
    /**
     * Get whether the user is a seller.
     * @return bool
     */
    public function getIsSeller() : bool;
    
    /**
     * Set whether the user is a customer.
     * @param bool $isCustomer
     * @return $this
     */
    public function setIsCustomer(bool $isCustomer) : self;
    
    /**
     * Get whether the user is a customer.
     * @return bool
     */
    public function getIsCustomer() : bool;
    
    /**
     * Set whether the user is a system admin.
     * @param bool $isSystemAdmin
     * @return $this
     */
    public function setIsSystemAdmin(bool $isSystemAdmin) : self;
    
    /**
     * Get whether the user is a system admin.
     * @return bool
     */
    public function getIsSystemAdmin() : bool;
    
    /**
     * Set whether the user account is active.
     * @param bool $isActive
     * @return $this
     */
    public function setIsActive(bool $isActive) : self;
    
    /**
     * Get whether the user account is active.
     * @return bool
     */
    public function getIsActive() : bool;
    
    /**
     * Set the deactivation timestamp, if any.
     * @param ?CarbonInterface $timestamp
     * @return $this
     */
    public function setDeactivatedAt(?CarbonInterface $timestamp) : self;
    
    /**
     * Get the deactivation timestamp, if any.
     * @return ?CarbonInterface
     */
    public function getDeactivatedAt() : ?CarbonInterface;
    
}
