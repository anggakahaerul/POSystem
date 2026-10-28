@extends('layouts.app')

@section('title', ($vendor ? 'Edit' : 'Tambah') . ' Vendor')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('vendors.index') }}">Vendor</a></li>
    <li class="breadcrumb-item active">{{ $vendor ? 'Edit' : 'Tambah' }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-{{ $vendor ? 'edit' : 'plus' }} mr-2"></i>
                    {{ $vendor ? 'Edit' : 'Tambah' }} Vendor
                </h3>
            </div>
            <form action="{{ $vendor ? route('vendors.update', $vendor) : route('vendors.store') }}" method="POST">
                @csrf
                @if($vendor)
                    @method('PUT')
                @endif
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="code">Kode Vendor <span class="text-danger">*</span></label>
                                <input type="text" name="code" id="code" 
                                    class="form-control @error('code') is-invalid @enderror" 
                                    value="{{ old('code', $vendor?->code ?? $suggestedCode) }}" 
                                    placeholder="VND0001"
                                    required>
                                @error('code')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="name">Nama Vendor <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" 
                                    class="form-control @error('name') is-invalid @enderror" 
                                    value="{{ old('name', $vendor?->name) }}" 
                                    placeholder="Nama perusahaan vendor"
                                    required>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email" 
                                    class="form-control @error('email') is-invalid @enderror" 
                                    value="{{ old('email', $vendor?->email) }}" 
                                    placeholder="vendor@example.com">
                                @error('email')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">Telepon</label>
                                <input type="text" name="phone" id="phone" 
                                    class="form-control @error('phone') is-invalid @enderror" 
                                    value="{{ old('phone', $vendor?->phone) }}" 
                                    placeholder="021-xxxxxxx">
                                @error('phone')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="contact_person">Contact Person</label>
                                <input type="text" name="contact_person" id="contact_person" 
                                    class="form-control @error('contact_person') is-invalid @enderror" 
                                    value="{{ old('contact_person', $vendor?->contact_person) }}" 
                                    placeholder="Nama kontak">
                                @error('contact_person')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="payment_term_id">Term of Payment</label>
                                <select name="payment_term_id" id="payment_term_id" class="form-control @error('payment_term_id') is-invalid @enderror">
                                    <option value="">-- Pilih Term of Payment --</option>
                                    @foreach(\App\Models\PaymentTerm::active()->orderBy('days')->get() as $term)
                                        <option value="{{ $term->id }}" {{ old('payment_term_id', $vendor?->payment_term_id) == $term->id ? 'selected' : '' }}>
                                            {{ $term->display_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('payment_term_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="bank_info">Informasi Rekening Bank</label>
                                <input type="text" name="bank_info" id="bank_info" 
                                    class="form-control @error('bank_info') is-invalid @enderror" 
                                    value="{{ old('bank_info', $vendor?->bank_info) }}" 
                                    placeholder="Nama bank, nomor rekening, atas nama">
                                @error('bank_info')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="npwp_type">Tipe NPWP</label>
                                <select name="npwp_type" id="npwp_type" class="form-control @error('npwp_type') is-invalid @enderror">
                                    <option value="">-- Pilih Tipe NPWP --</option>
                                    @foreach(\App\Models\Vendor::NPWP_TYPES as $type)
                                        <option value="{{ $type }}" {{ old('npwp_type', $vendor?->npwp_type) == $type ? 'selected' : '' }}>
                                            NPWP {{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('npwp_type')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="npwp">Nomor NPWP</label>
                                <input type="text" name="npwp" id="npwp" 
                                    class="form-control @error('npwp') is-invalid @enderror" 
                                    value="{{ old('npwp', $vendor?->npwp) }}" 
                                    placeholder="00.000.000.0-000.000">
                                @error('npwp')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address">Alamat</label>
                        <textarea name="address" id="address" 
                            class="form-control @error('address') is-invalid @enderror" 
                            rows="2" placeholder="Alamat lengkap vendor">{{ old('address', $vendor?->address) }}</textarea>
                        @error('address')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
                                {{ old('is_active', $vendor?->is_active ?? true) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">Vendor Aktif</label>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                    <a href="{{ route('vendors.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
