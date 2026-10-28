@extends('layouts.app')

@section('title', 'Aturan Approval PO')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="#">Master Data</a></li>
    <li class="breadcrumb-item active">Aturan Approval</li>
@endsection

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <div class="card shadow-sm border-0 mb-3" style="border-radius: 10px;">
            <div class="card-body p-3 d-flex flex-wrap align-items-center justify-content-between">
                <div class="d-flex align-items-center mb-2 mb-md-0">
                    <div class="rounded-circle p-3 mr-3 bg-light text-primary" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-clipboard-check fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 font-weight-bold">Matriks Aturan Approval PO</h5>
                        <small class="text-muted">Konfigurasi batas nominal approval dan jenjang persetujuan berdasarkan jabatan pembuat PO</small>
                    </div>
                </div>
                <div>
                    <button type="button" class="btn btn-primary font-weight-bold shadow-sm" onclick="openCreateModal()">
                        <i class="fas fa-plus mr-1"></i> Tambah Aturan Approval
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm" style="border-radius: 10px; overflow: hidden;">
    <div class="card-header bg-white py-3 border-bottom">
        <form method="GET" action="{{ route('approval-rules.index') }}" class="form-inline float-left">
            <label class="mr-2 text-muted small font-weight-bold"><i class="fas fa-filter mr-1"></i>Filter Departemen:</label>
            <select name="department_id" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                <option value="">Semua Aturan</option>
                <option value="global" {{ request('department_id') === 'global' ? 'selected' : '' }}>Aturan Global (Semua Dept)</option>
                @foreach ($departments as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                        {{ $dept->name }} ({{ $dept->code }})
                    </option>
                @endforeach
            </select>
            @if(request()->filled('department_id'))
                <a href="{{ route('approval-rules.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            @endif
        </form>
    </div>

    <div class="card-body p-0 table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th style="width: 40px;" class="text-center">#</th>
                    <th width="180">Nama Aturan</th>
                    <th width="150">Departemen</th>
                    <th width="180">Rentang Nominal</th>
                    <th>Jika Pemohon Staff/SPV</th>
                    <th>Jika Pemohon Manager L3</th>
                    <th class="text-center" width="80">Status</th>
                    <th class="text-center" style="width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rules as $index => $rule)
                    <tr>
                        <td class="align-middle text-center text-muted font-weight-bold">{{ $index + 1 }}</td>
                        <td class="align-middle">
                            <strong class="text-dark">{{ $rule->name }}</strong>
                        </td>
                        <td class="align-middle">
                            @if ($rule->department)
                                <span class="badge badge-info"><i class="fas fa-building mr-1"></i>{{ $rule->department->name }}</span>
                            @else
                                <span class="badge badge-secondary"><i class="fas fa-globe mr-1"></i>Global (Semua Dept)</span>
                            @endif
                        </td>
                        <td class="align-middle">
                            <span class="badge badge-light border text-dark px-2 py-1 font-weight-bold" style="font-size: 0.88rem;">
                                {{ $rule->amount_range_label }}
                            </span>
                        </td>
                        <!-- Pemohon Staff -->
                        <td class="align-middle">
                            <span class="badge badge-primary px-2 py-1 font-weight-bold">
                                <i class="fas fa-arrow-right mr-1"></i>{{ \App\Models\ApprovalRule::formatLevelLabel($rule->approver_for_staff) }}
                            </span>
                        </td>
                        <!-- Pemohon Manager L3 -->
                        <td class="align-middle">
                            <span class="badge badge-info px-2 py-1 font-weight-bold">
                                <i class="fas fa-arrow-right mr-1"></i>{{ \App\Models\ApprovalRule::formatLevelLabel($rule->approver_for_l3) }}
                            </span>
                        </td>
                        <td class="align-middle text-center">
                            <form action="{{ route('approval-rules.toggle', $rule->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-xs {{ $rule->is_active ? 'btn-success' : 'btn-secondary' }}" title="Klik untuk ubah status">
                                    {{ $rule->is_active ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </form>
                        </td>
                        <td class="align-middle text-center">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-warning btn-sm" title="Edit Aturan" onclick="openEditModal({{ $rule->id }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" title="Hapus Aturan" onclick="deleteRule({{ $rule->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fas fa-clipboard-list fa-3x mb-3 text-secondary" style="opacity: 0.5;"></i>
                            <h6 class="font-weight-bold">Belum Ada Aturan Approval</h6>
                            <p class="small mb-3">Klik tombol "Tambah Aturan Approval" untuk membuat batas nominal & jenjang persetujuan.</p>
                            <button type="button" class="btn btn-primary btn-sm" onclick="openCreateModal()">
                                <i class="fas fa-plus mr-1"></i> Tambah Aturan Approval
                            </button>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Form Aturan Approval (Add & Edit) -->
<div class="modal fade" id="ruleModal" tabindex="-1" role="dialog" aria-labelledby="ruleModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <form id="ruleForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                
                <div class="modal-header bg-dark text-white px-4 py-3">
                    <h5 class="modal-title font-weight-bold" id="ruleModalTitle">
                        <i class="fas fa-plus mr-1 text-warning"></i> Tambah Aturan Approval
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body p-4">
                    <!-- Basic Information -->
                    <div class="row">
                        <div class="col-md-7">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold">Nama Aturan <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="rule_name" class="form-control" placeholder="Contoh: Tier 1 (s/d Rp 10 Juta)" required>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold">Departemen</label>
                                <select name="department_id" id="rule_department_id" class="form-control">
                                    <option value="">Semua Departemen (Global)</option>
                                    @foreach ($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }} ({{ $dept->code }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Nominal Range -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold">Nominal Minimum (Rp) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" name="min_amount" id="rule_min_amount" class="form-control money-format" value="0" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold">Nominal Maksimum (Rp)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" name="max_amount" id="rule_max_amount" class="form-control money-format" placeholder="Tanpa batas">
                                </div>
                                <small class="form-text text-muted">Kosongkan jika nominal tidak memiliki batas atas.</small>
                            </div>
                        </div>
                    </div>

                    <!-- Matriks Approver Berdasarkan Level Pembuat PO -->
                    <div class="card border shadow-none mb-3 mt-2" style="border-radius: 8px; background-color: #f8fafc;">
                        <div class="card-header bg-light py-2 px-3 border-bottom">
                            <div class="font-weight-bold text-dark">
                                <i class="fas fa-user-shield text-primary mr-2"></i>Matriks Target Approver Berdasarkan Level Pembuat PO
                            </div>
                            <small class="text-muted">Untuk rentang nominal ini, tentukan tingkat approver yang dituju berdasarkan siapa yang membuat PO.</small>
                        </div>
                        <div class="card-body p-3">
                            <!-- Baris 1: Jika Pembuat adalah Staff / SPV -->
                            <div class="form-group row align-items-center mb-3">
                                <label class="col-md-5 col-form-label font-weight-bold text-dark">
                                    <span class="badge badge-secondary mr-1">Staff / SPV</span> Jika Pembuat PO adalah Staff:
                                </label>
                                <div class="col-md-7">
                                    <select name="approver_for_staff" id="rule_approver_for_staff" class="form-control" required>
                                        <option value="L3" selected>Manager Level 3 (Rekomendasi Default)</option>
                                        <option value="L4">Manager Level 4</option>
                                        <option value="L5">Manager Level 5</option>
                                        <option value="DIRECTOR">Direktur / Direksi</option>
                                        <option value="NONE">Tanpa Approval (Auto-Approve)</option>
                                    </select>
                                    <small class="form-text text-muted">Tingkat approver yang wajib menyetujui jika pembuat berposisi Staff.</small>
                                </div>
                            </div>

                            <!-- Baris 2: Jika Pembuat adalah Manager L3 -->
                            <div class="form-group row align-items-center mb-0">
                                <label class="col-md-5 col-form-label font-weight-bold text-dark">
                                    <span class="badge badge-primary mr-1">Manager L3</span> Jika Pembuat PO adalah Manager L3:
                                </label>
                                <div class="col-md-7">
                                    <select name="approver_for_l3" id="rule_approver_for_l3" class="form-control" required>
                                        <option value="L4" selected>Manager Level 4 (Naik 1 Tingkat)</option>
                                        <option value="L5">Manager Level 5</option>
                                        <option value="DIRECTOR">Direktur / Direksi</option>
                                        <option value="NONE">Tanpa Approval (Auto-Approve)</option>
                                    </select>
                                    <small class="form-text text-muted">Karena pembuat sudah Level 3, approver dialihkan ke level di atasnya.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="is_active" class="custom-control-input" id="rule_is_active" value="1" checked>
                            <label class="custom-control-label font-weight-bold text-dark" for="rule_is_active">Aktifkan aturan approval ini</label>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light px-4 py-3 justify-content-between">
                    <button type="button" class="btn btn-secondary px-3" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary px-4 font-weight-bold" id="btnSave">
                        <i class="fas fa-save mr-1"></i> Simpan Aturan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Form Delete Hidden -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    function formatMoney(value) {
        if (!value && value !== 0) return '';
        var num = parseInt(parseFloat(value)) || 0;
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    $(document).on('input', '.money-format', function() {
        var value = $(this).val().replace(/[^\d]/g, '');
        $(this).val(formatMoney(value));
    });

    function openCreateModal() {
        $('#ruleModalTitle').html('<i class="fas fa-plus mr-1 text-warning"></i> Tambah Aturan Approval');
        $('#formMethod').val('POST');
        $('#ruleForm').attr('action', '{{ route("approval-rules.store") }}');
        
        $('#rule_name').val('');
        $('#rule_department_id').val('');
        $('#rule_min_amount').val('0');
        $('#rule_max_amount').val('');
        $('#rule_approver_for_staff').val('L3');
        $('#rule_approver_for_l3').val('L4');
        $('#rule_is_active').prop('checked', true);

        $('#ruleModal').modal('show');
    }

    function openEditModal(id) {
        $('#ruleModalTitle').html('<i class="fas fa-edit mr-1 text-warning"></i> Edit Aturan Approval');
        $('#formMethod').val('PUT');
        $('#ruleForm').attr('action', '{{ url("approval-rules") }}/' + id);

        $.ajax({
            url: '{{ url("approval-rules") }}/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.success && res.data) {
                    var d = res.data;
                    $('#rule_name').val(d.name || '');
                    $('#rule_department_id').val(d.department_id || '');
                    $('#rule_min_amount').val(formatMoney(d.min_amount || 0));
                    $('#rule_max_amount').val(d.max_amount ? formatMoney(d.max_amount) : '');
                    $('#rule_approver_for_staff').val(d.approver_for_staff || 'L3');
                    $('#rule_approver_for_l3').val(d.approver_for_l3 || 'L4');
                    $('#rule_is_active').prop('checked', Boolean(d.is_active));

                    $('#ruleModal').modal('show');
                }
            },
            error: function() {
                Swal.fire('Error', 'Gagal memuat data aturan approval.', 'error');
            }
        });
    }

    function deleteRule(id) {
        Swal.fire({
            title: 'Hapus Aturan Approval?',
            text: 'Aturan approval ini akan dihapus secara permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                var form = $('#deleteForm');
                form.attr('action', '{{ url("approval-rules") }}/' + id);
                form.submit();
            }
        });
    }
</script>
@endpush
