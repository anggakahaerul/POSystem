{{--
    Generic Index (List) Template
    
    Usage:
    @extends('layouts.crud.index')
    
    @section('module_name', 'Kategori')
    @section('module_route', 'categories')
--}}
@extends('layouts.app')

@section('title', $title ?? 'Daftar ' . ($moduleName ?? 'Data'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">{{ $moduleName ?? 'Data' }}</li>
@endsection

@section('content')
    @include('components.datatable', [
        'id' => $tableId ?? 'data-table',
        'ajax' => $ajaxUrl ?? '#',
        'columns' => $columns ?? [],
        'createUrl' => $createUrl ?? null,
        'createText' => $createText ?? 'Tambah Baru',
        'title' => $tableTitle ?? null,
        'filters' => $filters ?? [],
        'order' => $order ?? [[0, 'desc']],
    ])
@endsection
