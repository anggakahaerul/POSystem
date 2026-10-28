@extends('layouts.app')

@section('title', 'Profil Pengguna')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Profil Saya</li>
@endsection

@section('content')
<div class="row">
    <!-- Left Column: User Summary Card -->
    <div class="col-xl-4 col-lg-5 mb-4">
        <div class="card card-primary card-outline shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
            <div class="card-body box-profile text-center pt-4 pb-4">
                <!-- User Avatar Initials -->
                <div class="d-inline-flex align-items-center justify-content-center shadow-sm mb-3"
                    style="width: 90px; height: 90px; border-radius: 50%; background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%); color: #ffffff; font-size: 2.2rem; font-weight: 700;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>

                <h3 class="profile-username text-center font-weight-bold mb-1 text-dark">{{ $user->name }}</h3>
                <p class="text-muted text-center small mb-2">
                    <i class="fas fa-envelope mr-1 text-secondary"></i>{{ $user->email }}
                </p>

                <!-- Roles Badges -->
                <div class="mb-3 text-center">
                    @forelse($user->roles as $role)
                        <span class="badge badge-primary px-3 py-1 font-weight-semibold shadow-xs" style="font-size: 0.82rem; border-radius: 50rem;">
                            <i class="fas fa-shield-alt mr-1"></i>{{ $role->name }}
                        </span>
                    @empty
                        <span class="badge badge-secondary px-2 py-1">User</span>
                    @endforelse
                </div>

                <hr class="my-3">

                <!-- Meta Details -->
                <div class="text-left px-2">
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-muted small"><i class="fas fa-building mr-2 text-primary"></i>Departemen</span>
                        <span class="font-weight-bold text-dark small text-right">
                            {{ $user->department?->name ?? 'Belum Ditentukan' }}
                        </span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-muted small"><i class="fas fa-map-marker-alt mr-2 text-danger"></i>Cabang</span>
                        <span class="font-weight-bold text-dark small text-right">
                            {{ $user->branch?->name ?? 'Head Office' }}
                        </span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-muted small"><i class="fas fa-calendar-alt mr-2 text-success"></i>Terdaftar Sejak</span>
                        <span class="font-weight-bold text-dark small text-right">
                            {{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}
                        </span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted small"><i class="fas fa-clock mr-2 text-info"></i>Terakhir Update</span>
                        <span class="font-weight-bold text-dark small text-right">
                            {{ $user->updated_at ? $user->updated_at->diffForHumans() : '-' }}
                        </span>
                    </div>
                </div>


            </div>
        </div>
    </div>

    <!-- Right Column: Tabs Form (Edit Info & Ganti Password) -->
    <div class="col-xl-8 col-lg-7">
        <div class="card card-tabs border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header p-0 pt-1 border-bottom bg-white">
                <ul class="nav nav-tabs" id="profileTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active font-weight-bold px-4 py-3" id="tab-info-link" data-toggle="pill" href="#tab-info" role="tab" aria-controls="tab-info" aria-selected="true">
                            <i class="fas fa-user-edit mr-2 text-primary"></i>Informasi Profil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold px-4 py-3" id="tab-password-link" data-toggle="pill" href="#tab-password" role="tab" aria-controls="tab-password" aria-selected="false">
                            <i class="fas fa-key mr-2 text-warning"></i>Ganti Password
                        </a>
                    </li>
                </ul>
            </div>

            <div class="card-body p-4">
                <div class="tab-content" id="profileTabContent">
                    <!-- Tab 1: Informasi Profil -->
                    <div class="tab-pane fade show active" id="tab-info" role="tabpanel" aria-labelledby="tab-info-link">
                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="form-group mb-3">
                                <label for="name" class="font-weight-bold small text-muted text-uppercase">Nama Lengkap <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0"><i class="fas fa-user text-muted"></i></span>
                                    </div>
                                    <input type="text" name="name" id="name" 
                                        class="form-control border-left-0 @error('name') is-invalid @enderror" 
                                        value="{{ old('name', $user->name) }}" 
                                        placeholder="Masukkan nama lengkap" required>
                                </div>
                                @error('name')
                                    <span class="text-danger small d-block mt-1 font-weight-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="email" class="font-weight-bold small text-muted text-uppercase">Alamat Email <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0"><i class="fas fa-envelope text-muted"></i></span>
                                    </div>
                                    <input type="email" name="email" id="email" 
                                        class="form-control border-left-0 @error('email') is-invalid @enderror" 
                                        value="{{ old('email', $user->email) }}" 
                                        placeholder="nama@domain.com" required>
                                </div>
                                @error('email')
                                    <span class="text-danger small d-block mt-1 font-weight-bold">{{ $message }}</span>
                                @enderror
                            </div>



                            <div class="mt-4 pt-2 border-top d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary px-4 shadow-sm" style="border-radius: 8px;">
                                    <i class="fas fa-save mr-2"></i> Simpan Informasi Profil
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Tab 2: Ganti Password -->
                    <div class="tab-pane fade" id="tab-password" role="tabpanel" aria-labelledby="tab-password-link">
                        <form action="{{ route('profile.password') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="alert alert-info border-0 shadow-xs mb-4" style="border-radius: 8px; font-size: 0.88rem;">
                                <i class="fas fa-info-circle mr-2"></i>
                                Untuk alasan keamanan, silakan masukkan password Anda saat ini sebelum menetapkan password baru.
                            </div>

                            <div class="form-group mb-3">
                                <label for="current_password" class="font-weight-bold small text-muted text-uppercase">Password Saat Ini <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0"><i class="fas fa-lock text-muted"></i></span>
                                    </div>
                                    <input type="password" name="current_password" id="current_password" 
                                        class="form-control border-left-0 @error('current_password') is-invalid @enderror" 
                                        placeholder="Masukkan password saat ini" required>
                                </div>
                                @error('current_password')
                                    <span class="text-danger small d-block mt-1 font-weight-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="password" class="font-weight-bold small text-muted text-uppercase">Password Baru <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0"><i class="fas fa-key text-muted"></i></span>
                                    </div>
                                    <input type="password" name="password" id="password" 
                                        class="form-control border-left-0 @error('password') is-invalid @enderror" 
                                        placeholder="Minimal 8 karakter" required>
                                </div>
                                @error('password')
                                    <span class="text-danger small d-block mt-1 font-weight-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label for="password_confirmation" class="font-weight-bold small text-muted text-uppercase">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0"><i class="fas fa-check-double text-muted"></i></span>
                                    </div>
                                    <input type="password" name="password_confirmation" id="password_confirmation" 
                                        class="form-control border-left-0" 
                                        placeholder="Ulangi password baru" required>
                                </div>
                            </div>

                            <div class="pt-2 border-top d-flex justify-content-end">
                                <button type="submit" class="btn btn-warning text-white font-weight-bold px-4 shadow-sm" style="border-radius: 8px;">
                                    <i class="fas fa-shield-alt mr-2"></i> Perbarui Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Auto-switch to password tab if password validation errors occur
        @if($errors->has('current_password') || $errors->has('password'))
            $('#profileTab a[href="#tab-password"]').tab('show');
        @endif
    });
</script>
@endpush
