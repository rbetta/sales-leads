<div class="form-field field-type-localizable-text">
	<label for="{{ $id }}[{{ $locale }}]">{{ $label }}</label>
	@if (count($values) > 1)
	<select name="{{ $name }}[selected]">
		@foreach (array_keys($values) as $locale)
		<option{{ old($toArrayDotSyntax($name . ".locale", $defaultLocale) === $locale ? ' selected' }}>{{ $locale }}</option>
		@endforeach
	</select>
	@endif
	<div data-section="values">
		@foreach ($values as $locale => $value)
    	<div data-locale="{{ $locale }}" style="display:{{ old($toArrayDotSyntax($name . ".locale", $defaultLocale) === $locale ? 'revert' : 'none' }}">
    		<input type="text" name="{{ $name }}[locales][{{ $locale }}]" id="{{ $id }}[{{ $locale }}]" value="{{ old($toArrayDotSyntax($name) . ".locales.$locale") ?? $value }}">
    	</div>
    	<div class="field-error">{{ $errors->first($toArrayDotSyntax($name) . ".locales.$locale") }}</div>
		@endforeach
	</div>
</div>
