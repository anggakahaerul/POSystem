@extends('layouts.app')

@section('title', ($paymentTerm ? 'Edit' : 'Tambah') . ' Term Pembayaran')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('payment-terms.index') }}">Term Pembayaran</a></li>
    <li class="breadcrumb-item active">{{ $paymentTerm ? 'Edit' : 'Tambah' }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-7">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-{{ $paymentTerm ? 'edit' : 'plus' }} mr-2"></i>
                    {{ $paymentTerm ? 'Edit' : 'Tambah' }} Term Pembayaran
                </h3>
            </div>
            <form action="{{ $paymentTerm ? route('payment-terms.update', $paymentTerm) : route('payment-terms.store') }}" method="POST" id="term-form">
                @csrf
                @if($paymentTerm)
                    @method('PUT')
                @endif
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Nama Term <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" 
                                    class="form-control @error('name') is-invalid @enderror" 
                                    value="{{ old('name', $paymentTerm?->name) }}" 
                                    placeholder="Contoh: COD, Net 30, DP 50%"
                                    required>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="payment_type">Tipe Pembayaran <span class="text-danger">*</span></label>
                                <select name="payment_type" id="payment_type" class="form-control @error('payment_type') is-invalid @enderror" required>
                                    <option value="full" {{ old('payment_type', $paymentTerm?->payment_type) === 'full' ? 'selected' : '' }}>Penuh (1x Bayar)</option>
                                    <option value="partial" {{ old('payment_type', $paymentTerm?->payment_type) === 'partial' ? 'selected' : '' }}>Cicilan (Beberapa Termin)</option>
                                </select>
                                @error('payment_type')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group" id="days-group">
                        <label for="days">Jatuh Tempo <span class="text-danger">*</span></label>
                        <div class="input-group" style="max-width: 200px;">
                            <input type="number" name="days" id="days" 
                                class="form-control @error('days') is-invalid @enderror" 
                                value="{{ old('days', $paymentTerm?->days ?? 0) }}" 
                                min="0"
                                required>
                            <div class="input-group-append">
                                <span class="input-group-text">hari</span>
                            </div>
                        </div>
                        <small class="text-muted">0 = Cash On Delivery (COD)</small>
                        @error('days')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Installments Section (shown when partial) -->
                    <div id="installments-section" style="display: none;">
                        <hr>
                        <h5><i class="fas fa-list-ol mr-2"></i>Konfigurasi Cicilan</h5>
                        <p class="text-muted small">Total persentase harus 100%</p>
                        
                        <table class="table table-bordered table-sm" id="installments-table">
                            <thead class="bg-light">
                                <tr>
                                    <th width="40">#</th>
                                    <th>Deskripsi</th>
                                    <th width="100">Persentase</th>
                                    <th width="120">Jatuh Tempo</th>
                                    <th width="50"></th>
                                </tr>
                            </thead>
                            <tbody id="installments-body">
                                <!-- Dynamic rows -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="text-right"><strong>Total:</strong></td>
                                    <td><strong id="total-percentage">0</strong>%</td>
                                    <td colspan="2">
                                        <button type="button" class="btn btn-success btn-sm btn-block" id="btn-add-installment">
                                            <i class="fas fa-plus"></i> Tambah
                                        </button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                        <input type="hidden" name="installments" id="installments-json">
                    </div>

                    <div class="form-group mt-3">
                        <label for="description">Deskripsi</label>
                        <textarea name="description" id="description" 
                            class="form-control @error('description') is-invalid @enderror" 
                            rows="2" placeholder="Keterangan tambahan">{{ old('description', $paymentTerm?->description) }}</textarea>
                        @error('description')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
                                {{ old('is_active', $paymentTerm?->is_active ?? true) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">Aktif</label>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                    <a href="{{ route('payment-terms.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-info-circle mr-2"></i>Panduan</h3>
            </div>
            <div class="card-body">
                <h6>Tipe Pembayaran:</h6>
                <ul class="small">
                    <li><strong>Penuh</strong>: 1 kali bayar (COD, Net 30, dll)</li>
                    <li><strong>Cicilan</strong>: Beberapa termin dengan persentase berbeda</li>
                </ul>
                
                <h6>Contoh Cicilan:</h6>
                <table class="table table-sm table-bordered">
                    <tr><th>DP 50%</th><td>50% hari ke-0, 50% hari ke-30</td></tr>
                    <tr><th>3x Cicilan</th><td>34% (0), 33% (30), 33% (60)</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Installment Row Template -->
<template id="installment-row-template">
    <tr class="installment-row">
        <td class="row-number">1</td>
        <td>
            <input type="text" class="form-control form-control-sm inst-desc" placeholder="Termin 1" required>
        </td>
        <td>
            <div class="input-group input-group-sm">
                <input type="number" class="form-control inst-pct" min="1" max="100" value="50" required>
                <div class="input-group-append"><span class="input-group-text">%</span></div>
            </div>
        </td>
        <td>
            <div class="input-group input-group-sm">
                <input type="number" class="form-control inst-days" min="0" value="0" required>
                <div class="input-group-append"><span class="input-group-text">hari</span></div>
            </div>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-danger btn-sm btn-remove-inst"><i class="fas fa-trash"></i></button>
        </td>
    </tr>
</template>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    var existingInstallments = @json($paymentTerm?->installments ?? []);
    
    // Parse installments if it's a JSON string
    if (typeof existingInstallments === 'string' && existingInstallments.length > 0) {
        try {
            existingInstallments = JSON.parse(existingInstallments);
        } catch(e) {
            existingInstallments = [];
        }
    }
    
    // Ensure it's an array
    if (!Array.isArray(existingInstallments)) {
        existingInstallments = [];
    }

    // Toggle installments section
    $('#payment_type').on('change', function() {
        toggleInstallmentsSection(false);
    });

    function toggleInstallmentsSection(isInitial) {
        var type = $('#payment_type').val();
        if (type === 'partial') {
            $('#installments-section').show();
            $('#days-group').hide();
            // Only add default rows if no existing installments and no rows yet
            if ($('.installment-row').length === 0 && !isInitial) {
                addInstallmentRow({description: 'Down Payment', percentage: 50, days: 0});
                addInstallmentRow({description: 'Pelunasan', percentage: 50, days: 30});
            }
        } else {
            $('#installments-section').hide();
            $('#days-group').show();
        }
    }

    // Add installment row
    $('#btn-add-installment').on('click', function() {
        addInstallmentRow({description: 'Termin ' + ($('.installment-row').length + 1), percentage: 0, days: 0});
    });

    function addInstallmentRow(data) {
        var template = $('#installment-row-template').html();
        var $row = $(template);
        
        $row.find('.inst-desc').val(data.description || '');
        $row.find('.inst-pct').val(data.percentage || 0);
        $row.find('.inst-days').val(data.days || 0);
        
        $('#installments-body').append($row);
        updateRowNumbers();
        updateTotalPercentage();
    }

    // Remove installment row
    $(document).on('click', '.btn-remove-inst', function() {
        if ($('.installment-row').length > 2) {
            $(this).closest('.installment-row').remove();
            updateRowNumbers();
            updateTotalPercentage();
        } else {
            Swal.fire({icon: 'warning', title: 'Minimal 2 termin!', text: 'Cicilan harus memiliki minimal 2 termin.'});
        }
    });

    // Update total percentage
    $(document).on('input', '.inst-pct', updateTotalPercentage);

    function updateTotalPercentage() {
        var total = 0;
        $('.inst-pct').each(function() {
            total += parseInt($(this).val()) || 0;
        });
        $('#total-percentage').text(total);
        
        if (total !== 100) {
            $('#total-percentage').addClass('text-danger').removeClass('text-success');
        } else {
            $('#total-percentage').addClass('text-success').removeClass('text-danger');
        }
    }

    function updateRowNumbers() {
        $('.installment-row').each(function(i) {
            $(this).find('.row-number').text(i + 1);
        });
    }

    // Form submit - build installments JSON
    $('#term-form').on('submit', function(e) {
        var type = $('#payment_type').val();
        
        if (type === 'partial') {
            var total = 0;
            var installments = [];
            
            $('.installment-row').each(function() {
                var pct = parseInt($(this).find('.inst-pct').val()) || 0;
                total += pct;
                installments.push({
                    description: $(this).find('.inst-desc').val(),
                    percentage: pct,
                    days: parseInt($(this).find('.inst-days').val()) || 0
                });
            });
            
            if (total !== 100) {
                e.preventDefault();
                Swal.fire({icon: 'error', title: 'Total harus 100%!', text: 'Total persentase saat ini: ' + total + '%'});
                return false;
            }
            
            $('#installments-json').val(JSON.stringify(installments));
        }
    });

    // Load existing installments FIRST (before toggle)
    if (existingInstallments && existingInstallments.length > 0) {
        existingInstallments.forEach(function(inst) {
            addInstallmentRow(inst);
        });
    }

    // Initial state - pass true to indicate it's initial load
    toggleInstallmentsSection(true);
});
</script>
@endpush
