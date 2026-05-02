<?php
declare(strict_types = 1);
namespace App\View\Components\AdminForm;

use App\View\Components\AbstractFormField;
use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * A component for rendering a set of radio buttons.
 * @author Randall Betta
 *
 */
class AdminFieldRadio extends AbstractFormField
{
    
    /**
     * Construct an instance of this class.
     * @param string $idPrefix The HTML "id" attribute prefix for each radio
     * button. A hyphen and the field value will be suffixed to this for
     * each radio button.
     * @param string $label The human-readable label for the set of radio
     * buttons.
     * @param string $name The HTML "name" attribute for the radio buttons.
     * @param array $options An array whose keys are the values of the radio
     * buttons, and whose values are their corresponding human-readable labels.
     * @param string|null $value The HTML "value" attribute for the selected
     * option (if any).
     */
    public function __construct(
        public string $idPrefix,
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
        return view('components.form.admin.field-radio');
    }
    
}
