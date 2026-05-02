<?php
declare(strict_types=1);
namespace Carcosa\Core\DataObjects\Localization;

use Carcosa\Core\DataObjects\AbstractSoftDeletableTimestampedUuid;
use Carcosa\Core\Json\Adapters\CarbonJsonAdapter;
use Illuminate\Contracts\Validation\Validator;

/**
 * A class that represents text that can be localized into multiple locales.
 */
class LocalizedTextData extends AbstractSoftDeletableTimestampedUuid
{
    
}
