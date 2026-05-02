<?php
declare(strict_types = 1);
namespace Carcosa\Core\Service;

/**
 * An enumeration that defines whether and how to limit results
 * retrieved via a service based on a Boolean search criterion. 
 * @author Randall Betta
 *
 */
enum BooleanCriterion
{
    case TRUE;      // Limit the criterion to only true values.
    case FALSE;     // Limit the criterion to only false values.
    case BOTH;      // Don't limit the criterion.
}
