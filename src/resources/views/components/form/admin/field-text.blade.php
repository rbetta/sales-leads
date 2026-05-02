<div class="form-field">
	<label for="{{ $id }}">{{ $label }}</label>
	<input type="text" name="{{ $name }}" id="{{ $id }}" value="{{ old($toArrayDotSyntax($name)) ?? $value }}">
	<div class="field-error">{{ $errors->first($toArrayDotSyntax($name)) }}</div>
</div>
