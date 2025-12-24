{{--
    Generic Detail/Show Template
    
    Usage:
    @extends('layouts.crud.show')
--}}
@extends('layouts.app')

@section('title', 'Detail ' . ($moduleName ?? 'Data'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route($moduleRoute . '.index') }}">{{ $moduleName ?? 'Data' }}</a></li>
    <li class="breadcrumb-item active">Detail</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-{{ $mainWidth ?? 12 }}">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="{{ $icon ?? 'fas fa-info-circle' }} mr-2"></i>
                    Detail {{ $moduleName ?? 'Data' }}
                </h3>
                <div class="card-tools">
                    @if(isset($editUrl))
                        <a href="{{ $editUrl }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </a>
                    @endif
                    @if(isset($deleteUrl))
                        <form action="{{ $deleteUrl }}" method="POST" class="d-inline" id="delete-form-show">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-danger btn-sm" 
                                onclick="confirmDelete('delete-form-show')">
                                <i class="fas fa-trash mr-1"></i> Hapus
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    @foreach($details ?? [] as $detail)
                    <tr>
                        <th style="width: {{ $labelWidth ?? '200px' }}">{{ $detail['label'] }}</th>
                        <td>
                            @if(isset($detail['html']))
                                {!! $detail['html'] !!}
                            @elseif(isset($detail['badge']))
                                <span class="badge badge-{{ $detail['badge'] }}">{{ $detail['value'] }}</span>
                            @elseif(isset($detail['money']))
                                Rp {{ number_format($detail['value'], 0, ',', '.') }}
                            @elseif(isset($detail['date']))
                                {{ $detail['value'] ? \Carbon\Carbon::parse($detail['value'])->format($detail['date']) : '-' }}
                            @else
                                {{ $detail['value'] ?? '-' }}
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </table>
            </div>
            <div class="card-footer">
                <a href="{{ route($moduleRoute . '.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
                @yield('footer-actions')
            </div>
        </div>
    </div>
    
    @if(isset($sideContent))
    <div class="col-md-{{ 12 - ($mainWidth ?? 12) }}">
        {!! $sideContent !!}
    </div>
    @endif
</div>

@yield('additional-content')
@endsection
