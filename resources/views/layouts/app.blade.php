<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'PO System') }} | @yield('title', 'Dashboard')</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('assets/AdminLTE3') }}/plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet"
        href="{{ asset('assets/AdminLTE3') }}/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet"
        href="{{ asset('assets/AdminLTE3') }}/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <!-- Toastr -->
    <link rel="stylesheet" href="{{ asset('assets/AdminLTE3') }}/plugins/toastr/toastr.min.css">
    <!-- DataTables -->
    <link rel="stylesheet"
        href="{{ asset('assets/AdminLTE3') }}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet"
        href="{{ asset('assets/AdminLTE3') }}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/AdminLTE3') }}/plugins/select2/css/select2.min.css">
    <link rel="stylesheet"
        href="{{ asset('assets/AdminLTE3') }}/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('assets/AdminLTE3') }}/dist/css/adminlte.min.css">
    <!-- Modern UI Theme -->
    <link rel="stylesheet" href="{{ asset('css/modern-theme.css') }}?v={{ filemtime(public_path('css/modern-theme.css')) }}">

    @stack('styles')
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">
    <div class="wrapper">

        <!-- Preloader -->
        {{-- <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__shake" src="{{ asset('assets/AdminLTE3/dist/img/AdminLTELogo.png') }}"
                alt="Logo" height="60" width="60">
        </div> --}}

        <!-- Navbar -->
        @include('layouts.partials.navbar')

        <!-- Main Sidebar Container -->
        @include('layouts.partials.sidebar')

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">@yield('title', 'Dashboard')</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                @yield('breadcrumb')
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </section>
        </div>

        <!-- Footer -->
        @include('layouts.partials.footer')

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
        </aside>
    </div>

    <!-- jQuery -->
    <script src="{{ asset('assets/AdminLTE3/plugins/jquery/jquery.min.js') }}"></script>
    <!-- jQuery UI -->
    <script src="{{ asset('assets/AdminLTE3/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('assets/AdminLTE3/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- overlayScrollbars -->
    <script src="{{ asset('assets/AdminLTE3/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <!-- SweetAlert2 -->
    <script src="{{ asset('assets/AdminLTE3/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- Toastr -->
    <script src="{{ asset('assets/AdminLTE3/plugins/toastr/toastr.min.js') }}"></script>
    <!-- DataTables -->
    <script src="{{ asset('assets/AdminLTE3/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/AdminLTE3/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/AdminLTE3/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/AdminLTE3/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <!-- Select2 -->
    <script src="{{ asset('assets/AdminLTE3/plugins/select2/js/select2.full.min.js') }}"></script>
    <!-- InputMask -->
    <script src="{{ asset('assets/AdminLTE3/plugins/inputmask/jquery.inputmask.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('assets/AdminLTE3/dist/js/adminlte.min.js') }}"></script>
    <!-- Modal CRUD Helper -->
    <script src="{{ asset('js/modal-crud.js') }}"></script>

    <!-- Global Scripts -->
    <script>
        // CSRF Token for AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Global notification functions
        window.showSuccess = function(message) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: message,
                confirmButtonColor: '#28a745'
            });
        };

        window.showError = function(message) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: message,
                confirmButtonColor: '#dc3545'
            });
        };

        window.showWarning = function(message) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian!',
                text: message,
                confirmButtonColor: '#ffc107'
            });
        };

        window.showInfo = function(message) {
            Swal.fire({
                icon: 'info',
                title: 'Informasi',
                text: message,
                confirmButtonColor: '#17a2b8'
            });
        };

        // Confirm Delete Dialog
        window.confirmDelete = function(formId, message = 'Data akan dihapus permanen!') {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        };

        // Show session flash messages
        @if (session('success'))
            showSuccess("{{ session('success') }}");
        @endif

        @if (session('error'))
            showError("{{ session('error') }}");
        @endif

        @if (session('warning'))
            showWarning("{{ session('warning') }}");
        @endif

        @if (session('info'))
            showInfo("{{ session('info') }}");
        @endif

        // Initialize Select2
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap4'
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
