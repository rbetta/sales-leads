<div class="form-field">
	<fieldset>
		<legend>{{ $label }}</legend>
		@foreach ($options as $optionValue => $optionLabel)
		<div class="form-field-components">
    		<input type="checkbox" name="{{ $toIndexedArraySyntax($name, $loop->index) }}" id="{{ $idPrefix }}-{{ $optionValue }}" value="{{ $optionValue }}"{{ (in_array("$optionValue", old($toArrayDotSyntax($stripEmptyArraySyntax($name))) ?? [], true) ? ' checked' : (in_array("$optionValue", $values ?? [], true) ? ' checked' : '')) }}>
    		<label for="{{ $idPrefix }}-{{ $optionValue }}">{{ $optionLabel }}</label>
    	</div>
    	@endforeach
    </fieldset>
    <div class="field-error">{{ $errors->first($toArrayDotSyntax($name, true)) }}</div>
</div>
