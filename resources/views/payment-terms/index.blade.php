@extends('layouts.app')

@section('title', 'Term Pembayaran')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Term Pembayaran</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-calendar-alt mr-2"></i>Daftar Term Pembayaran
        </h3>
        <div class="card-tools">
            <a href="{{ route('payment-terms.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Tambah Term
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="bg-primary text-white">
                    <tr>
                        <th width="50">#</th>
                        <th>Nama Term</th>
                        <th width="100" class="text-center">Tipe</th>
                        <th width="120" class="text-center">Jatuh Tempo</th>
                        <th>Detail</th>
                        <th width="100" class="text-center">Status</th>
                        <th width="120" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paymentTerms as $index => $term)
                    <tr>
                        <td>{{ $paymentTerms->firstItem() + $index }}</td>
                        <td><strong>{{ $term->name }}</strong></td>
                        <td class="text-center">
                            @if($term->payment_type === 'partial')
                                <span class="badge badge-warning">Cicilan</span>
                            @else
                                <span class="badge badge-info">Penuh</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($term->payment_type === 'partial')
                                <small class="text-muted">Sesuai termin</small>
                            @elseif($term->days == 0)
                                <span class="badge badge-success">COD</span>
                            @else
                                <span class="badge badge-secondary">{{ $term->days }} hari</span>
                            @endif
                        </td>
                        <td>
                            @if($term->payment_type === 'partial' && $term->installments)
                                @php
                                    $installments = is_array($term->installments) ? $term->installments : json_decode($term->installments, true);
                                @endphp
                                @if(is_array($installments))
                                    @foreach($installments as $i => $inst)
                                        <small>{{ $inst['percentage'] ?? 0 }}% ({{ $inst['days'] ?? 0 }}hr){{ $i < count($installments) - 1 ? ', ' : '' }}</small>
                                    @endforeach
                                @else
                                    {{ $term->description ?: '-' }}
                                @endif
                            @else
                                {{ $term->description ?: '-' }}
                            @endif
                        </td>
                        <td class="text-center">
                            @if($term->is_active)
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-secondary">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ route('payment-terms.edit', $term) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('payment-terms.destroy', $term) }}" method="POST" class="d-inline" id="delete-form-{{ $term->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete('delete-form-{{ $term->id }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-calendar-alt fa-3x mb-3 d-block"></i>
                            Belum ada data term pembayaran
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $paymentTerms->links() }}</div>
    </div>
</div>
@endsection
