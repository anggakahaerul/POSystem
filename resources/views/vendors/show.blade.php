@extends('layouts.app')

@section('title', 'Detail Vendor')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('vendors.index') }}">Vendor</a></li>
    <li class="breadcrumb-item active">{{ $vendor->name }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-building mr-2"></i>Detail Vendor
                </h3>
                <div class="card-tools">
                    @if($vendor->is_active)
                        <span class="badge badge-success px-3 py-2">Aktif</span>
                    @else
                        <span class="badge badge-secondary px-3 py-2">Nonaktif</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="200">Kode Vendor</th>
                        <td><code>{{ $vendor->code }}</code></td>
                    </tr>
                    <tr>
                        <th>Nama</th>
                        <td><strong>{{ $vendor->name }}</strong></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $vendor->email ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th>Telepon</th>
                        <td>{{ $vendor->phone ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th>Contact Person</th>
                        <td>{{ $vendor->contact_person ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td>{{ $vendor->address ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th>Tipe NPWP</th>
                        <td>
                            @if($vendor->npwp_type)
                                <span class="badge badge-info px-2 py-1">{{ $vendor->npwp_type }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Nomor NPWP</th>
                        <td>{{ $vendor->npwp ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th>Informasi Bank</th>
                        <td>{{ $vendor->bank_info ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th>Term of Payment (TOP)</th>
                        <td>
                            @if($vendor->paymentTerm)
                                <span class="badge badge-light border text-primary font-weight-bold px-2 py-1" style="font-size: 0.88rem;">
                                    <i class="fas fa-file-invoice-dollar mr-1"></i>{{ $vendor->paymentTerm->display_name }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Jumlah PO</th>
                        <td><span class="badge badge-info">{{ $vendor->purchase_orders_count }} Purchase Order</span></td>
                    </tr>
                    <tr>
                        <th>Dibuat</th>
                        <td>{{ $vendor->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>
            <div class="card-footer">
                <a href="{{ route('vendors.edit', $vendor) }}" class="btn btn-warning">
                    <i class="fas fa-edit mr-1"></i> Edit
                </a>
                <a href="{{ route('vendors.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
                @if($vendor->purchase_orders_count == 0)
                <form action="{{ route('vendors.destroy', $vendor) }}" method="POST" class="d-inline float-right" id="delete-form-vendor">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-danger" onclick="confirmDelete('delete-form-vendor')">
                        <i class="fas fa-trash mr-1"></i> Hapus
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
