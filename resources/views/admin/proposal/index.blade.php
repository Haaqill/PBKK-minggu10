@extends('adminlte::page')

@section('title', 'Manajemen Proposal - Admin Panel')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0 text-dark font-weight-bold">
                <i class="fas fa-file-invoice text-success mr-2"></i> Manajemen Proposal Mahasiswa
            </h1>
            <p class="text-muted mb-0">Tinjau seluruh proposal yang masuk dari mahasiswa.</p>
        </div>
        <a href="{{ route('admin.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Dashboard
        </a>
    </div>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card card-success card-outline shadow-sm">
        <div class="card-header">
            <h3 class="card-title font-weight-bold">
                <i class="fas fa-list mr-1 text-success"></i> Daftar Seluruh Proposal
            </h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Judul Proposal</th>
                            <th>Pengusul</th>
                            <th>Status Saat Ini</th>
                            <th>Waktu Pengajuan</th>
                            <th style="width: 150px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($proposals as $index => $proposal)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="font-weight-bold">
                                    {{ $proposal->judul }}
                                    <div class="text-muted small text-truncate" style="max-width: 480px;">
                                        {{ $proposal->deskripsi }}
                                    </div>
                                </td>
                                <td>
                                    <i class="fas fa-user-graduate mr-1 text-secondary"></i>
                                    {{ $proposal->mahasiswa->name ?? 'Mahasiswa' }}
                                </td>
                                <td>
                                    <span class="badge badge-{{ $proposal->status === 'disetujui' ? 'success' : ($proposal->status === 'ditolak' ? 'danger' : 'warning') }} px-2 py-1">
                                        {{ ucfirst($proposal->status) }}
                                    </span>
                                </td>
                                <td class="text-muted small">
                                    {{ $proposal->created_at ? $proposal->created_at->format('d M Y H:i') : '-' }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('proposal.show', $proposal) }}" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye mr-1"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Belum ada proposal yang masuk.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

