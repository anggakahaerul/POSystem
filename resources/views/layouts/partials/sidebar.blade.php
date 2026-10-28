<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('dashboard') }}" class="brand-link d-flex align-items-center" style="white-space: normal; padding: 0.8125rem 0.8rem;">
        <img src="{{ asset('assets/AdminLTE3/dist/img/AdminLTELogo.png') }}" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8; max-height: 33px; margin-left: 0.2rem; margin-right: 0.6rem;">
        <span class="brand-text font-weight-bold" style="font-size: 0.84rem; line-height: 1.25; display: inline-block;">Purchase &amp; Asset Management</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- Master Data (Khusus HR dan Administrator dengan izin terkait) -->
                @php
                    $canAccessMasterData = Auth::user() && 
                        Auth::user()->roles->pluck('name')->intersect(['Admin', 'Administrator', 'HR'])->isNotEmpty() &&
                        (Auth::user()->hasRole('Admin') || Auth::user()->canAny([
                            'view departments', 'view approval-rules', 'view categories', 
                            'view products', 'view vendors', 'view branches', 'view payment-terms'
                        ]));
                @endphp
                @if($canAccessMasterData)
                <li class="nav-item {{ request()->is('categories*') || request()->is('products*') || request()->is('vendors*') || request()->is('branches*') || request()->is('payment-terms*') || request()->is('departments*') || request()->is('approval-rules*') || request()->is('asset-types*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('categories*') || request()->is('products*') || request()->is('vendors*') || request()->is('branches*') || request()->is('payment-terms*') || request()->is('departments*') || request()->is('approval-rules*') || request()->is('asset-types*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-database"></i>
                        <p>
                            Master Data
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @can('view departments')
                        <li class="nav-item">
                            <a href="{{ route('departments.index') }}" class="nav-link {{ request()->routeIs('departments.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Departemen</p>
                            </a>
                        </li>
                        @endcan
                        @can('view approval-rules')
                        <li class="nav-item">
                            <a href="{{ route('approval-rules.index') }}" class="nav-link {{ request()->routeIs('approval-rules.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Aturan Approval</p>
                            </a>
                        </li>
                        @endcan
                        @can('view categories')
                        <li class="nav-item">
                            <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Kategori</p>
                            </a>
                        </li>
                        @endcan
                        @can('view products')
                        <li class="nav-item">
                            <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Produk</p>
                            </a>
                        </li>
                        @endcan
                        @can('view vendors')
                        <li class="nav-item">
                            <a href="{{ route('vendors.index') }}" class="nav-link {{ request()->routeIs('vendors.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Vendor</p>
                            </a>
                        </li>
                        @endcan
                        @can('view branches')
                        <li class="nav-item">
                            <a href="{{ route('branches.index') }}" class="nav-link {{ request()->routeIs('branches.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Cabang</p>
                            </a>
                        </li>
                        @endcan
                        @can('view payment-terms')
                        <li class="nav-item">
                            <a href="{{ route('payment-terms.index') }}" class="nav-link {{ request()->routeIs('payment-terms.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Term Pembayaran</p>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endif

                <!-- Transactions -->
                @php
                    $canAccessTransaksi = Auth::user() && (
                        Auth::user()->hasRole('Admin') ||
                        Auth::user()->canAny([
                            'view purchase-requests',
                            'view purchase-orders',
                            'create purchase-orders',
                            'view goods-receipts',
                            'view tax',
                            'review tax',
                            'approve purchase-orders'
                        ]) ||
                        Auth::user()->canApprovePO()
                    );
                @endphp
                @if($canAccessTransaksi)
                <li class="nav-item {{ request()->is('purchase-requests*') || request()->is('purchase-orders*') || request()->is('tax-reviews*') || request()->is('goods-receipts*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('purchase-requests*') || request()->is('purchase-orders*') || request()->is('tax-reviews*') || request()->is('goods-receipts*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>
                            Transaksi
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @can('view purchase-requests')
                        <li class="nav-item">
                            <a href="{{ route('purchase-requests.index') }}" class="nav-link {{ request()->routeIs('purchase-requests.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Permintaan Pembelian</p>
                            </a>
                        </li>
                        @endcan
                        @can('view purchase-orders')
                        <li class="nav-item">
                            <a href="{{ route('purchase-orders.index') }}" class="nav-link {{ request()->routeIs('purchase-orders.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Daftar PO</p>
                            </a>
                        </li>
                        @endcan
                        @can('view goods-receipts')
                        <li class="nav-item">
                            <a href="{{ route('goods-receipts.index') }}" class="nav-link {{ request()->routeIs('goods-receipts.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Daftar Barang Masuk</p>
                            </a>
                        </li>
                        @endcan
                        @can('create purchase-orders')
                        <li class="nav-item">
                            <a href="{{ route('purchase-orders.create') }}" class="nav-link {{ request()->routeIs('purchase-orders.create') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Buat PO Baru</p>
                            </a>
                        </li>
                        @endcan
                        @canany(['view tax', 'review tax'])
                        <li class="nav-item">
                            <a href="{{ route('tax-reviews.index') }}" class="nav-link {{ request()->routeIs('tax-reviews.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>
                                    Review Pajak
                                    @if(isset($pendingTaxReviewCount) && $pendingTaxReviewCount > 0)
                                        <span class="badge badge-warning right">{{ $pendingTaxReviewCount }}</span>
                                    @endif
                                </p>
                            </a>
                        </li>
                        @endcanany
                        @if(Auth::user()->can('approve purchase-orders') || Auth::user()->canApprovePO())
                        <li class="nav-item">
                            <a href="{{ route('purchase-orders.index', ['status' => 'pending']) }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Approval PO</p>
                                @if(isset($pendingPOCount) && $pendingPOCount > 0)
                                    <span class="badge badge-warning right">{{ $pendingPOCount }}</span>
                                @endif
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                <!-- Asset Management -->
                <li class="nav-item {{ request()->is('assets*') || request()->is('asset-*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('assets*') || request()->is('asset-*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-boxes"></i>
                        <p>
                            Asset Management
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="#" class="nav-link" onclick="event.preventDefault();">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Daftar Aset</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- User Management -->
                @php
                    $canAccessUserMgmt = Auth::user() && (
                        Auth::user()->hasRole('Admin') ||
                        Auth::user()->canAny(['view users', 'view roles', 'view permissions'])
                    );
                @endphp
                @if($canAccessUserMgmt)
                <li class="nav-item {{ request()->is('users*') || request()->is('roles*') || request()->is('permissions*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('users*') || request()->is('roles*') || request()->is('permissions*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users-cog"></i>
                        <p>
                            User Management
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @can('view users')
                        <li class="nav-item">
                            <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Daftar User</p>
                            </a>
                        </li>
                        @endcan
                        @can('view roles')
                        <li class="nav-item">
                            <a href="{{ route('roles.index') }}" class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Role</p>
                            </a>
                        </li>
                        @endcan
                        @can('view permissions')
                        <li class="nav-item">
                            <a href="{{ route('permissions.index') }}" class="nav-link {{ request()->routeIs('permissions.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Permission</p>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endif

                <!-- Settings (Admin Only) -->
                @if(Auth::user()->hasRole('Admin'))
                <li class="nav-item {{ request()->is('settings*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('settings*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>
                            Pengaturan
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('settings.email') }}" class="nav-link {{ request()->routeIs('settings.email*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Email & SMTP</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('settings.utilization') }}" class="nav-link {{ request()->routeIs('settings.utilization*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Utilitas Deploy</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

            </ul>
        </nav>
    </div>
</aside>
