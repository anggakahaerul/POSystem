{{--
    Generic Create/Edit Form Template
    
    Usage:
    @extends('layouts.crud.form')
    
    Define in controller:
    return view('categories.form', [
        'moduleName' => 'Kategori',
        'moduleRoute' => 'categories',
        'action' => route('categories.store'),
        'method' => 'POST',
        'data' => null,
        'fields' => [...],
    ]);
--}}
@extends('layouts.app')

@section('title', (isset($data) && $data ? 'Edit ' : 'Tambah ') . ($moduleName ?? 'Data'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route($moduleRoute . '.index') }}">{{ $moduleName ?? 'Data' }}</a></li>
    <li class="breadcrumb-item active">{{ isset($data) && $data ? 'Edit' : 'Tambah' }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-{{ $formWidth ?? 12 }}">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="{{ $icon ?? 'fas fa-edit' }} mr-2"></i>
                    {{ isset($data) && $data ? 'Edit' : 'Tambah' }} {{ $moduleName ?? 'Data' }}
                </h3>
            </div>
            
            @include('components.form', [
                'action' => $action,
                'method' => $method ?? 'POST',
                'fields' => $fields,
                'data' => $data ?? null,
                'submitText' => $submitText ?? 'Simpan',
                'backUrl' => route($moduleRoute . '.index'),
                'formId' => $formId ?? 'crud-form',
                'enctype' => $enctype ?? 'application/x-www-form-urlencoded',
            ])
        </div>
    </div>
    
    @if(isset($sidebar))
    <div class="col-md-{{ 12 - ($formWidth ?? 12) }}">
        {!! $sidebar !!}
    </div>
    @endif
</div>
@endsection
