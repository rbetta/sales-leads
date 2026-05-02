<div class="form-field">
	<fieldset>
		<legend>{{ $label }}</legend>
		@foreach ($options as $optionValue => $optionLabel)
    	<div class="form-field-components">
    		<input type="radio" name="{{ $name }}" id="{{ $idPrefix }}-{{ $optionValue }}" value="{{ $optionValue }}"{{ ("$optionValue" === (string) old($toArrayDotSyntax($name)) ? ' checked' : (("$optionValue" === "$value") ? ' checked' : '')) }}>
    		<label for="{{ $idPrefix }}-{{ $optionValue }}">{{ $optionLabel }}</label>
    	</div>
    	@endforeach
    </fieldset>
    <div class="field-error">{{ $errors->first($toArrayDotSyntax($name)) }}</div>
</div>
