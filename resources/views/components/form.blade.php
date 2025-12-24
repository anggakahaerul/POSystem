{{--
    Dynamic Form Component
    
    Usage:
    @include('components.form', [
        'action' => route('categories.store'),
        'method' => 'POST',
        'fields' => [
            ['name' => 'name', 'label' => 'Nama', 'type' => 'text', 'required' => true],
            ['name' => 'description', 'label' => 'Deskripsi', 'type' => 'textarea'],
            ['name' => 'category_id', 'label' => 'Kategori', 'type' => 'select', 'options' => $categories, 'option_value' => 'id', 'option_label' => 'name'],
        ],
        'data' => $category ?? null,
        'submitText' => 'Simpan'
    ])
--}}

<form action="{{ $action }}" method="POST" id="{{ $formId ?? 'dynamic-form' }}" enctype="{{ $enctype ?? 'application/x-www-form-urlencoded' }}">
    @csrf
    @if(isset($method) && strtoupper($method) !== 'POST')
        @method($method)
    @endif

    <div class="card-body">
        @foreach($fields as $field)
            @php
                $fieldName = $field['name'];
                $fieldValue = old($fieldName, isset($data) ? ($data->{$fieldName} ?? '') : ($field['value'] ?? ''));
                $isRequired = $field['required'] ?? false;
                $fieldClass = 'form-control ' . ($errors->has($fieldName) ? 'is-invalid' : '');
                $colClass = $field['col'] ?? 'col-md-12';
            @endphp

            @if(!isset($field['row_start']) || $field['row_start'])
                <div class="row">
            @endif
            
            <div class="{{ $colClass }}">
                <div class="form-group">
                    @if($field['type'] !== 'hidden')
                        <label for="{{ $fieldName }}">
                            {{ $field['label'] }}
                            @if($isRequired)<span class="text-danger">*</span>@endif
                        </label>
                    @endif

                    @switch($field['type'])
                        @case('text')
                        @case('email')
                        @case('password')
                        @case('number')
                        @case('date')
                        @case('time')
                        @case('datetime-local')
                            <input 
                                type="{{ $field['type'] }}" 
                                class="{{ $fieldClass }}" 
                                id="{{ $fieldName }}" 
                                name="{{ $fieldName }}" 
                                value="{{ $field['type'] === 'password' ? '' : $fieldValue }}"
                                placeholder="{{ $field['placeholder'] ?? '' }}"
                                {{ $isRequired ? 'required' : '' }}
                                {{ isset($field['readonly']) && $field['readonly'] ? 'readonly' : '' }}
                                {{ isset($field['disabled']) && $field['disabled'] ? 'disabled' : '' }}
                                @if(isset($field['min'])) min="{{ $field['min'] }}" @endif
                                @if(isset($field['max'])) max="{{ $field['max'] }}" @endif
                                @if(isset($field['step'])) step="{{ $field['step'] }}" @endif
                            >
                            @break

                        @case('textarea')
                            <textarea 
                                class="{{ $fieldClass }}" 
                                id="{{ $fieldName }}" 
                                name="{{ $fieldName }}" 
                                rows="{{ $field['rows'] ?? 3 }}"
                                placeholder="{{ $field['placeholder'] ?? '' }}"
                                {{ $isRequired ? 'required' : '' }}
                            >{{ $fieldValue }}</textarea>
                            @break

                        @case('select')
                            <select 
                                class="{{ $fieldClass }} {{ $field['select2'] ?? false ? 'select2' : '' }}" 
                                id="{{ $fieldName }}" 
                                name="{{ $fieldName }}"
                                {{ $isRequired ? 'required' : '' }}
                                {{ isset($field['multiple']) && $field['multiple'] ? 'multiple' : '' }}
                            >
                                @if(!isset($field['multiple']) || !$field['multiple'])
                                    <option value="">-- Pilih {{ $field['label'] }} --</option>
                                @endif
                                @foreach($field['options'] as $option)
                                    @php
                                        $optValue = is_object($option) ? $option->{$field['option_value'] ?? 'id'} : ($option['value'] ?? $option);
                                        $optLabel = is_object($option) ? $option->{$field['option_label'] ?? 'name'} : ($option['label'] ?? $option);
                                    @endphp
                                    <option value="{{ $optValue }}" {{ $fieldValue == $optValue ? 'selected' : '' }}>
                                        {{ $optLabel }}
                                    </option>
                                @endforeach
                            </select>
                            @break

                        @case('checkbox')
                            <div class="form-check">
                                <input 
                                    type="checkbox" 
                                    class="form-check-input" 
                                    id="{{ $fieldName }}" 
                                    name="{{ $fieldName }}" 
                                    value="1"
                                    {{ $fieldValue ? 'checked' : '' }}
                                >
                                <label class="form-check-label" for="{{ $fieldName }}">{{ $field['checkbox_label'] ?? $field['label'] }}</label>
                            </div>
                            @break

                        @case('radio')
                            @foreach($field['options'] as $option)
                                @php
                                    $optValue = is_array($option) ? $option['value'] : $option;
                                    $optLabel = is_array($option) ? $option['label'] : $option;
                                @endphp
                                <div class="form-check">
                                    <input 
                                        type="radio" 
                                        class="form-check-input" 
                                        id="{{ $fieldName }}_{{ $optValue }}" 
                                        name="{{ $fieldName }}" 
                                        value="{{ $optValue }}"
                                        {{ $fieldValue == $optValue ? 'checked' : '' }}
                                    >
                                    <label class="form-check-label" for="{{ $fieldName }}_{{ $optValue }}">{{ $optLabel }}</label>
                                </div>
                            @endforeach
                            @break

                        @case('file')
                            <div class="custom-file">
                                <input 
                                    type="file" 
                                    class="custom-file-input {{ $errors->has($fieldName) ? 'is-invalid' : '' }}" 
                                    id="{{ $fieldName }}" 
                                    name="{{ $fieldName }}"
                                    {{ $isRequired ? 'required' : '' }}
                                    {{ isset($field['accept']) ? 'accept='.$field['accept'] : '' }}
                                    {{ isset($field['multiple']) && $field['multiple'] ? 'multiple' : '' }}
                                >
                                <label class="custom-file-label" for="{{ $fieldName }}">Pilih file</label>
                            </div>
                            @if(isset($data) && $data->{$fieldName})
                                <small class="text-muted">File saat ini: {{ $data->{$fieldName} }}</small>
                            @endif
                            @break

                        @case('hidden')
                            <input type="hidden" id="{{ $fieldName }}" name="{{ $fieldName }}" value="{{ $fieldValue }}">
                            @break

                        @case('money')
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input 
                                    type="text" 
                                    class="{{ $fieldClass }} money-format" 
                                    id="{{ $fieldName }}" 
                                    name="{{ $fieldName }}" 
                                    value="{{ $fieldValue }}"
                                    placeholder="{{ $field['placeholder'] ?? '0' }}"
                                    {{ $isRequired ? 'required' : '' }}
                                >
                            </div>
                            @break

                        @default
                            <input 
                                type="text" 
                                class="{{ $fieldClass }}" 
                                id="{{ $fieldName }}" 
                                name="{{ $fieldName }}" 
                                value="{{ $fieldValue }}"
                                placeholder="{{ $field['placeholder'] ?? '' }}"
                                {{ $isRequired ? 'required' : '' }}
                            >
                    @endswitch

                    @error($fieldName)
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror

                    @if(isset($field['help']))
                        <small class="form-text text-muted">{{ $field['help'] }}</small>
                    @endif
                </div>
            </div>

            @if(!isset($field['row_end']) || $field['row_end'])
                </div>
            @endif
        @endforeach
    </div>

    <div class="card-footer">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-1"></i> {{ $submitText ?? 'Simpan' }}
        </button>
        @if(isset($backUrl))
            <a href="{{ $backUrl }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        @endif
    </div>
</form>

@push('scripts')
<script>
$(document).ready(function() {
    // Custom file input label
    $('input[type="file"]').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName || 'Pilih file');
    });

    // Money format
    $('.money-format').on('input', function() {
        var value = $(this).val().replace(/[^\d]/g, '');
        $(this).val(value.replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
    });
});
</script>
@endpush
