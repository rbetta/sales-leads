<div class="form-field">
<label for="{{ $id }}">{{ $label }}</label>
	<select name="{{ $name }}" id="{{ $id }}">
	@foreach ($options as $optionValue => $optionLabel)
		<option value="{{ $optionValue }}"{{ ($optionValue === old($toArrayDotSyntax($name))) ? ' selected' : (($optionValue === $value) ? ' selected' : '') }}>{{ $optionLabel }}</option>
	@endforeach
	</select>
    <div class="field-error">{{ $errors->first($toArrayDotSyntax($name)) }}</div>
</div>
