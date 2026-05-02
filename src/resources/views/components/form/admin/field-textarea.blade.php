<div class="form-field">
    <label for="{{ $id }}">{{ $label }}</label>
    <textarea name="{{ $name }}" id="{{ $id }}">{{ old($toArrayDotSyntax($name)) ?? $value }}</textarea>
    <div class="field-error">{{ $errors->first($toArrayDotSyntax($name)) }}</div>
</div>
