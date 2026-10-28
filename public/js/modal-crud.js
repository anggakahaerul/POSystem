/**
 * Universal Modal CRUD Handler
 * Clean Architecture Frontend Helper for AJAX Modals
 */

window.ModalCrud = {
    // Open Modal for Create
    openCreate: function(modalId, formId, title, options = {}) {
        const modal = $(modalId);
        const form = $(formId);
        
        // Reset form
        form[0].reset();
        form.find('input[name="_method"]').remove();
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').remove();
        form.find('input[type="hidden"][name="id"]').val('');
        
        // Set action url if provided
        if (options.actionUrl) {
            form.attr('action', options.actionUrl);
        }
        
        // Set title
        modal.find('.modal-title span.title-text').text(title || 'Tambah Data');
        
        // Custom callback before open
        if (typeof options.beforeShow === 'function') {
            options.beforeShow(form);
        }
        
        modal.modal('show');
    },

    // Open Modal for Edit
    openEdit: function(modalId, formId, url, title, options = {}) {
        const modal = $(modalId);
        const form = $(formId);
        
        // Reset form & errors
        form[0].reset();
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').remove();
        
        // Set method to PUT
        if (form.find('input[name="_method"]').length === 0) {
            form.append('<input type="hidden" name="_method" value="PUT">');
        } else {
            form.find('input[name="_method"]').val('PUT');
        }
        
        // Set action URL
        if (options.updateUrl) {
            form.attr('action', options.updateUrl);
        }
        
        // Set title
        modal.find('.modal-title span.title-text').text(title || 'Edit Data');

        // Show loading state in modal
        const submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true);
        
        // Fetch data via AJAX
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function(response) {
                const data = response.data || response;
                
                // Populate fields
                $.each(data, function(key, value) {
                    const input = form.find(`[name="${key}"]`);
                    if (input.length) {
                        if (input.is(':checkbox')) {
                            input.prop('checked', Boolean(value));
                        } else if (input.is(':radio')) {
                            form.find(`[name="${key}"][value="${value}"]`).prop('checked', true);
                        } else if (input.hasClass('select2')) {
                            input.val(value).trigger('change');
                        } else {
                            input.val(value);
                        }
                    }
                });

                // Custom callback after populate
                if (typeof options.afterPopulate === 'function') {
                    options.afterPopulate(data, form);
                }

                submitBtn.prop('disabled', false);
                modal.modal('show');
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Mengambil Data',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan saat memuat data.'
                });
            }
        });
    },

    // Submit form via AJAX
    submitAjax: function(formId, modalId, options = {}) {
        const form = $(formId);
        const modal = $(modalId);
        const submitBtn = form.find('button[type="submit"]');
        const originalBtnHtml = submitBtn.html();
        
        // Clear previous validation states
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').remove();
        
        // Disable button & show spinner
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');

        // FormData for file support
        const formData = new FormData(form[0]);

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function(response) {
                submitBtn.prop('disabled', false).html(originalBtnHtml);
                modal.modal('hide');

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message || 'Data berhasil disimpan.',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    if (typeof options.onSuccess === 'function') {
                        options.onSuccess(response);
                    } else {
                        window.location.reload();
                    }
                });
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).html(originalBtnHtml);

                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors || {};
                    $.each(errors, function(field, messages) {
                        const input = form.find(`[name="${field}"]`);
                        if (input.length) {
                            input.addClass('is-invalid');
                            if (input.closest('.input-group').length) {
                                input.closest('.input-group').after(`<div class="invalid-feedback d-block">${messages[0]}</div>`);
                            } else {
                                input.after(`<div class="invalid-feedback d-block">${messages[0]}</div>`);
                            }
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menyimpan',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan pada server.'
                    });
                }
            }
        });
    },

    // Confirm and Delete via AJAX
    deleteAjax: function(url, message = 'Data ini akan dihapus secara permanen!', options = {}) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="fas fa-trash mr-1"></i> Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Terhapus!',
                            text: response.message || 'Data berhasil dihapus.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            if (typeof options.onSuccess === 'function') {
                                options.onSuccess(response);
                            } else {
                                window.location.reload();
                            }
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Menghapus',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan pada server.'
                        });
                    }
                });
            }
        });
    }
};
