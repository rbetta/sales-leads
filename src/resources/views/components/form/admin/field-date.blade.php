<div class="form-field">
	<label for="{{ $id }}">{{ $label }}</label>
	<input type="hidden" name="{{ $name }}" id="{{ $id }}" value="{{ old($toArrayDotSyntax($name)) ?? $value }}">
	<input type="text" class="form-field-date">
	<div class="field-error">{{ $errors->first($toArrayDotSyntax($name)) }}</div>
</div>
