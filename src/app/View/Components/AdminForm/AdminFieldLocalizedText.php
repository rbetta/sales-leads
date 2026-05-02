<?php
declare(strict_types = 1);
namespace App\View\Components\AdminForm;

use App\View\Components\AbstractFormField;
use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * A component for rendering a single-line localizable text field.
 * @author Randall Betta
 *
 */
class AdminFieldLocalizedText extends AbstractFormField
{
    
    /**
     * Construct an instance of this class.
     * @param string $id The HTML "id" attribute for the field. (An
     * array index for each locale will be appended to this.)
     * @param string $label The human-readable label for the field.
     * @param string $name The name for this field. (An array index for
     * each associated locale will be appended to this.)
     * @param array $values The HTML "value" attributes for the field,
     * keyed by their locales.
     * @param string $defaultLocale The initial locale to display.
     */
    public function __construct(
        public string $id,
        public string $label,
        public string $name,
        public string|null $values,
        public string $defaultLocale,
    ) {
        
    }

    /**
     * Render this component.
     * @return View
     */
    public function render() : View
    {
        return view('components.form.admin.field-localized-text');
    }
    
}
