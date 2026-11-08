@extends('adminlte::page')

@section('title', 'Dashboard Admin')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0 text-dark font-weight-bold">Dashboard Backoffice</h1>
            <p class="text-muted mb-0">Panel kendali data dan otorisasi terintegrasi AdminLTE.</p>
        </div>
        <div>
            @if(auth()->user()->hasRole('Admin') || auth()->user()->role === 'Admin')
                <span class="badge badge-success px-3 py-2" style="font-size: 0.95rem;">
                    <i class="fas fa-shield-alt mr-1"></i> Peran: Admin (Akses Penuh)
                </span>
            @else
                <span class="badge badge-info px-3 py-2" style="font-size: 0.95rem;">
                    <i class="fas fa-user-tag mr-1"></i> Peran: Staf (Akses Terbatas)
                </span>
            @endif
        </div>
    </div>
@stop

@section('content')
    {{-- Notifikasi Session --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Widget Dashboard Agregat (Minimal 3 query model count()) --}}
    <div class="row">
        {{-- Widget 1: Total Proyek --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info shadow-sm">
                <div class="inner">
                    <h3>{{ $totalProjects }}</h3>
                    <p>Total Data Proyek</p>
                </div>
                <div class="icon">
                    <i class="fas fa-project-diagram"></i>
                </div>
                <a href="{{ route('project.index') }}" class="small-box-footer">
                    Kelola Proyek <i class="fas fa-arrow-circle-right ml-1"></i>
                </a>
            </div>
        </div>

        {{-- Widget 2: Total Proposal --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success shadow-sm">
                <div class="inner">
                    <h3>{{ $totalProposals }}</h3>
                    <p>Total Pengajuan Proposal</p>
                </div>
                <div class="icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <a href="{{ route('admin.proposal.index') }}" class="small-box-footer">
                    Lihat Proposal <i class="fas fa-arrow-circle-right ml-1"></i>
                </a>
            </div>
        </div>

        {{-- Widget 3: Total Pengguna Terdaftar --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning shadow-sm">
                <div class="inner">
                    <h3>{{ $totalUsers }}</h3>
                    <p>Total Pengguna Sistem</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="#" class="small-box-footer">
                    Pengguna Aktif <i class="fas fa-check-circle ml-1"></i>
                </a>
            </div>
        </div>

        {{-- Widget 4: Total Kotak Sampah (Data Terhapus) --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger shadow-sm">
                <div class="inner">
                    <h3>{{ $totalRecycleBin }}</h3>
                    <p>Proyek di Recycle Bin</p>
                </div>
                <div class="icon">
                    <i class="fas fa-trash-alt"></i>
                </div>
                @can('delete-projects')
                    <a href="{{ route('project.recycle-bin') }}" class="small-box-footer">
                        Buka Kotak Sampah <i class="fas fa-arrow-circle-right ml-1"></i>
                    </a>
                @else
                    <span class="small-box-footer text-muted" style="cursor: not-allowed;">
                        <i class="fas fa-lock mr-1"></i> Hak Khusus Admin
                    </span>
                @endcan
            </div>
        </div>
    </div>

    {{-- Kartu Aksi Cepat & Navigasi Hak Akses --}}
    <div class="row">
        <div class="col-md-7">
            <div class="card card-primary card-outline shadow-sm">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-folder-open mr-2 text-primary"></i> 5 Proyek Terbaru
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('project.create') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus mr-1"></i> Tambah Proyek Baru
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Judul Proyek</th>
                                    <th>Tema Agent</th>
                                    <th>Pembuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentProjects as $item)
                                    <tr>
                                        <td class="font-weight-bold">{{ Str::limit($item->judul, 35) }}</td>
                                        <td>
                                            <span class="badge badge-secondary">{{ $item->tema_agent }}</span>
                                        </td>
                                        <td>{{ $item->user->name ?? 'N/A' }}</td>
                                        <td>
                                            <a href="{{ route('project.show', $item) }}" class="btn btn-xs btn-info">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">Belum ada data proyek.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <a href="{{ route('project.index') }}" class="btn btn-sm btn-outline-primary">
                        Lihat Seluruh Data Proyek &rarr;
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            {{-- Panel Hak Akses Eksklusif --}}
            <div class="card card-dark card-outline shadow-sm">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-user-shield mr-2 text-warning"></i> Kontrol Hak Akses (RBAC Spatie)
                    </h3>
                </div>
                <div class="card-body">
                    <p class="text-muted text-sm">
                        Sistem otorisasi ini diatur secara dinamis melalui database menggunakan paket Spatie Laravel Permission.
                    </p>
                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-eye mr-2 text-info"></i> Melihat Dashboard & Data</span>
                            <span class="badge badge-success"><i class="fas fa-check"></i> Admin & Staf</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-edit mr-2 text-primary"></i> Input & Edit Proyek/Proposal</span>
                            <span class="badge badge-success"><i class="fas fa-check"></i> Admin & Staf</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-trash-alt mr-2 text-danger"></i> <strong>Hapus Data (Hak Eksklusif)</strong></span>
                            @can('delete-projects')
                                <span class="badge badge-danger"><i class="fas fa-check"></i> Diizinkan (Admin)</span>
                            @else
                                <span class="badge badge-secondary"><i class="fas fa-ban"></i> Terkunci (Hanya Admin)</span>
                            @endcan
                        </li>
                    </ul>

                    <div class="mt-3">
                        <h6 class="font-weight-bold text-secondary">Aksi Eksklusif Admin:</h6>
                        @can('delete-projects')
                            <div class="btn-group d-flex" role="group">
                                <a href="{{ route('admin.nilai') }}" class="btn btn-outline-success mr-2">
                                    <i class="fas fa-award mr-1"></i> Input Nilai Dosen
                                </a>
                                <a href="{{ route('project.recycle-bin') }}" class="btn btn-outline-danger">
                                    <i class="fas fa-trash-restore mr-1"></i> Kelola Recycle Bin
                                </a>
                            </div>
                        @else
                            <div class="alert alert-light border text-muted p-2 mb-0 text-sm">
                                <i class="fas fa-info-circle mr-1 text-secondary"></i>
                                Anda saat ini login sebagai <strong>Staf</strong>. Tombol hapus dan menu evaluasi nilai dinonaktifkan secara otomatis.
                            </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .small-box { border-radius: 0.5rem; }
        .small-box .icon { font-size: 55px; opacity: 0.25; right: 15px; top: 12px; }
        .small-box:hover .icon { opacity: 0.45; }
    </style>
@stop
