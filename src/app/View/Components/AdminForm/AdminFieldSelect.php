<?php
declare(strict_types = 1);
namespace App\View\Components\AdminForm;

use App\View\Components\AbstractFormField;
use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * A component for rendering a select dropdown.
 * @author Randall Betta
 *
 */
class AdminFieldSelect extends AbstractFormField
{
    
    /**
     * Construct an instance of this class.
     * @param string $id The HTML "id" attribute for the field.
     * @param string $label The human-readable label for the field.
     * @param string $name The HTML "name" attribute for the field.
     * @param array $options An array whose keys are HTML option elements'
     * "value" attributes, and whose values are their corresponding labels.
     * @param string|null $value The HTML "value" attribute for the
     * selected option (if any).
     */
    public function __construct(
        public string $id,
        public string $label,
        public string $name,
        public array $options,
        public string|null $value = null,
    ) {
        
    }

    /**
     * Render this component.
     * @return View
     */
    public function render() : View
    {
        return view('components.form.admin.field-select');
    }
    
}
