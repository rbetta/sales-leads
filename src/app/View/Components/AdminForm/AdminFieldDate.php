<?php
declare(strict_types = 1);
namespace App\View\Components\AdminForm;

use App\View\Components\AbstractFormField;
use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * A component for rendering a date selector.
 * @author Randall Betta
 *
 */
class AdminFieldDate extends AbstractFormField
{
    
    /**
     * Construct an instance of this class.
     * @param string $id The HTML "id" attribute for the underlying hidden field containing the date.
     * @param string $label The human-readable label for the field.
     * @param string $name The HTML "name" attribute for the underlying hidden field containing the date.
     * @param string|null $value The HTML "value" attribute for the underlying hidden field containing the date.
     */
    public function __construct(
        public string $id,
        public string $label,
        public string $name,
        public string|null $value,
    ) {
        
    }

    /**
     * Render this component.
     * @return View
     */
    public function render() : View
    {
        return view('components.form.admin.field-date');
    }
    
}
