<?php
declare(strict_types = 1);
namespace App\View\Components\AdminForm;

use App\View\Components\AbstractFormField;
use Carcosa\Core\Util\ListFormatter;
use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * A component for rendering a set of two Boolean-value radio buttons.
 * @author Randall Betta
 */
class AdminFieldRadioBoolean extends AbstractFormField
{
    
    /**
     * @param array $options An array whose keys are the values of the radio
     * buttons, and whose values are their corresponding human-readable labels.
     */
    public array $options;
    
    /**
     * Construct an instance of this class.
     * @param string $idPrefix The HTML "id" attribute prefix for each radio
     * button. A hyphen and the field value will be suffixed to this for
     * each radio button.
     * @param string $label The human-readable label for the set of radio
     * buttons.
     * @param string $name The HTML "name" attribute for the radio buttons.
     * @param string $truelabel The human-readable label for the radio
     * button that represents a Boolean true value (its HTML "value"
     * attribute will be "1").
     * @param string $falselabel The human-readable label for the radio
     * button that represents a Boolean false value (its HTML "value"
     * attribute will be "0").
     * @param string|bool|null $value The HTML "value" attribute for the
     * selected option (if any). This must be one of the following:
     * "1" (indicating the true value), "0" (indicating the false value),
     * a Boolean value, or the empty string or null (both indicating no
     * selected value).
     * @throws \InvalidArgumentException If the supplied value is
     * none of the following: "1", "0", true, false, "", or null.
     */
    public function __construct(
        public string $idPrefix,
        public string $label,
        public string $name,
        public string $trueLabel,
        public string $falseLabel,
        public string|bool|null $value = null,
    ) {
        
        // Validate the selected value.
        $validValues = ["1", "0", true, false, "", null];
        if (! in_array($value, $validValues, true)) {
            
            // Construct a list of expected values, as a string.
            $formatter  = \App::make(ListFormatter::class);
            $expected   = $listFormatter->format($validValues);
            
            throw new \InvalidArgumentException(
                "The invalid value \"$value\" was supplied to " .
                __METHOD__ . " (expected: one of {" . $expected . "})."
            );
            
        }
        
        // PHP converts Boolean values to string as follow:
        //
        //  true    => "1"
        //  false   => ""
        //
        // This is contrary to most languages, which usually use
        // 0 to indicate false. We correct this here, so that
        // false values are properly handled when comparing them
        // against Boolean values that are cast to strings using
        // saner type juggling rules (e.g. from a database).
        //
        if (is_bool($value)) {
            $this->value = $value ? "1" : "0";
        }
        
        // Force the array of options into a Boolean set.
        $this->options = [
            "1" => $trueLabel,
            "0" => $falseLabel,
        ];
        
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
