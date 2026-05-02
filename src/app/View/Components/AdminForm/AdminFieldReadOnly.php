<?php
declare(strict_types = 1);
namespace App\View\Components\AdminForm;

use App\View\Components\AbstractFormField;
use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * A component for rendering a read-only field.
 * @author Randall Betta
 *
 */
class AdminFieldReadOnly extends AbstractFormField
{
    
    /**
     * Construct an instance of this class.
     * @param string|null $value The read-only value.
     */
    public function __construct(
        public string $label,
        public string|null $value,
    ) {
        
    }

    /**
     * Render this component.
     * @return View
     */
    public function render() : View
    {
        return view('components.form.admin.field-read-only');
    }
    
}
