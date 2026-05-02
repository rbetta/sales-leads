<?php
declare(strict_types = 1);
namespace App\View\Components\AdminForm;

use App\View\Components\AbstractFormField;
use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * A component for rendering a set of checkboxes.
 * @author Randall Betta
 *
 */
class AdminFieldCheckboxes extends AbstractFormField
{
    
    /**
     * The array of all checkboxes' labels, keyed by their values.
     * @var string[]
     */
    public array $options = [];
    
    /**
     * The array of all selected checkboxes' values.
     * @var string[]
     */
    public array $values = [];
    
    /**
     * Construct an instance of this class.
     * @param string $idPrefix The HTML "id" attribute prefix for each
     * checkbox. A hyphen and the field value will be suffixed to this for
     * each checkbox.
     * @param string $label The human-readable label for the set of
     * checkboxes.
     * @param string $name The HTML "name" attribute for the checkboxes.
     * @param array $options An array whose keys are the values of the
     * checkboxes, and whose values are their corresponding human-readable
     * labels.
     * @param string[]|null $values The HTML "value" attributes for the
     * selected checkboxes (if any).
     */
    public function __construct(
        public string $idPrefix,
        public string $label,
        public string $name,
        array $options,
        array|null $values = null,
    ) {
        
        // Ensure that all available and selected options are
        // cast to strings, so that we can perform comparisons
        // on them safely in the template logic no matter what
        // their underlying values are. This avoids weird bugs
        // associated with type juggling edge cases.
        
        // First, handle all options.
        $newOptions = [];
        foreach($options as $key => $value) {
            $newOptions[(string) $key] = (string) $value;
        }
        $this->options = $newOptions;
        
        // Next, handle all selected values.
        $this->values = array_map(fn($v) => (string) $v, $values ?? []);
    }

    /**
     * Render this component.
     * @return View
     */
    public function render() : View
    {
        return view('components.form.admin.field-checkboxes');
    }
    
}
