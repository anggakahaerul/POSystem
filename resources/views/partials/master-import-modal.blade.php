<!-- Modal Import Excel Shared -->
<div class="modal fade" id="masterImportModal" tabindex="-1" role="dialog" aria-labelledby="masterImportModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header text-white px-4 py-3" style="background-color: #1e293b !important;">
                <div>
                    <h5 class="modal-title font-weight-bold mb-0" id="masterImportModalTitle">
                        <i class="fas fa-file-excel mr-2 text-success"></i>Import Data <span id="importModalMasterName"></span>
                    </h5>
                    <small class="text-white-50">Upload file Excel (.xlsx, .xls) atau CSV (.csv)</small>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" style="font-size: 1.5rem; opacity: 0.85;">
                    <span>&times;</span>
                </button>
            </div>
            <form id="masterImportForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body px-4 py-3">
                    <!-- Step 1: Download Template -->
                    <div class="alert alert-light border mb-3 p-3" style="background-color: #f8fafc; border-radius: 8px;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong class="text-dark d-block" style="font-size: 0.88rem;">Belum memiliki format Excel?</strong>
                                <small class="text-muted">Gunakan template resmi kami agar format kolom sesuai.</small>
                            </div>
                            <a href="#" id="btnDownloadTemplate" class="btn btn-outline-info btn-sm font-weight-bold flex-shrink-0 ml-2" style="border-radius: 6px;">
                                <i class="fas fa-download mr-1"></i> Template Excel
                            </a>
                        </div>
                    </div>

                    <!-- Step 2: Choose File -->
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark mb-1">
                            Pilih File Excel / CSV <span class="text-danger">*</span>
                        </label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="import_file_input" name="file" accept=".xlsx,.xls,.csv,.txt" required>
                            <label class="custom-file-label" for="import_file_input" id="import_file_label">Pilih file excel...</label>
                        </div>
                        <small class="text-muted mt-1 d-block">
                            <i class="fas fa-info-circle mr-1 text-info"></i>Format didukung: <strong>.xlsx, .xls, .csv</strong> (Maksimal 10 MB).
                        </small>
                    </div>

                    <!-- Option: Update existing -->
                    <div class="form-group mb-0">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="update_existing" name="update_existing" value="1" checked>
                            <label class="custom-control-label font-weight-normal text-dark" for="update_existing" style="font-size: 0.86rem; cursor: pointer;">
                                <strong>Perbarui data yang sudah ada</strong> jika Kode atau Nama ditemukan (Edit Massal).
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3 justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-success px-4 font-weight-bold" id="btnSubmitImport">
                        <i class="fas fa-file-upload mr-1"></i> Upload & Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    var currentMasterImportType = '';

    function openMasterImportModal(type, title) {
        currentMasterImportType = type;
        $('#importModalMasterName').text(title || '');
        $('#btnDownloadTemplate').attr('href', "{{ url('master-data') }}/" + type + "/template");
        $('#import_file_input').val('');
        $('#import_file_label').text('Pilih file excel...');
        $('#update_existing').prop('checked', true);
        $('#btnSubmitImport').prop('disabled', false).html('<i class="fas fa-file-upload mr-1"></i> Upload & Import');
        $('#masterImportModal').modal('show');
    }

    $(document).on('change', '#import_file_input', function(e) {
        var file = e.target.files[0];
        if (file) {
            $('#import_file_label').text(file.name);
        } else {
            $('#import_file_label').text('Pilih file excel...');
        }
    });

    $(document).on('submit', '#masterImportForm', function(e) {
        e.preventDefault();

        if (!currentMasterImportType) {
            Swal.fire('Error', 'Tipe master tidak valid.', 'error');
            return;
        }

        var fileInput = document.getElementById('import_file_input');
        if (!fileInput.files.length) {
            Swal.fire('Perhatian', 'Silakan pilih file Excel terlebih dahulu.', 'warning');
            return;
        }

        var formData = new FormData(this);
        var submitBtn = $('#btnSubmitImport');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Memproses Import...');

        $.ajax({
            url: "{{ url('master-data') }}/" + currentMasterImportType + "/import",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                $('#masterImportModal').modal('hide');
                
                var detailHtml = '<div class="text-left mt-2 p-2 bg-light rounded" style="font-size: 0.88rem;">' +
                    '<div class="text-success"><i class="fas fa-plus-circle mr-1"></i> Data Baru: <strong>' + (res.added || 0) + '</strong></div>' +
                    '<div class="text-info"><i class="fas fa-sync-alt mr-1"></i> Diperbarui: <strong>' + (res.updated || 0) + '</strong></div>' +
                    (res.failed > 0 ? '<div class="text-danger"><i class="fas fa-exclamation-triangle mr-1"></i> Gagal/Dilewati: <strong>' + res.failed + '</strong></div>' : '') +
                    '</div>';

                if (res.errors && res.errors.length) {
                    detailHtml += '<div class="text-left mt-2 small text-danger" style="max-height: 100px; overflow-y: auto;">' +
                        res.errors.join('<br>') +
                        '</div>';
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Import Berhasil!',
                    html: (res.message || 'Data berhasil diproses.') + detailHtml,
                    confirmButtonText: 'OK, Muat Ulang'
                }).then(function() {
                    window.location.reload();
                });
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).html('<i class="fas fa-file-upload mr-1"></i> Upload & Import');
                var errorMsg = 'Terjadi kesalahan saat mengunggah file.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Import',
                    text: errorMsg
                });
            }
        });
    });
</script>
