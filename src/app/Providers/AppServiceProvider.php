<?php

namespace App\Providers;

use App\View\Components\AdminForm\AdminFieldCheckboxes;
use App\View\Components\AdminForm\AdminFieldDate;
use App\View\Components\AdminForm\AdminFieldRadio;
use App\View\Components\AdminForm\AdminFieldRadioBoolean;
use App\View\Components\AdminForm\AdminFieldReadOnly;
use App\View\Components\AdminForm\AdminFieldSelect;
use App\View\Components\AdminForm\AdminFieldText;
use App\View\Components\AdminForm\AdminFieldTextArea;
use App\View\Components\AdminForm\Sort\AdminSortItem;
use App\View\Components\ClientForm\ClientFieldLocalizedText;
use App\View\Components\ClientForm\ClientFieldReadOnly;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    
    /**
     * Register any application services.
     */
    public function register(): void
    {
        
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        
        // Register Blade components for back-end admin form fields.
        Blade::component('admin-field-checkboxes',      AdminFieldCheckboxes::class);
        Blade::component('admin-field-date',            AdminFieldDate::class);
        Blade::component('admin-field-radio',           AdminFieldRadio::class);
        Blade::component('admin-field-radio-boolean',   AdminFieldRadioBoolean::class);
        Blade::component('admin-field-read-only',       AdminFieldReadOnly::class);
        Blade::component('admin-field-select',          AdminFieldSelect::class);
        Blade::component('admin-field-text',            AdminFieldText::class);
        Blade::component('admin-field-textarea',        AdminFieldTextArea::class);
        Blade::component('admin-sort-item',             AdminSortItem::class);
        
    }
}
