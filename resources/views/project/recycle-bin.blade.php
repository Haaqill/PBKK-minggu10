@extends('adminlte::page')

@section('title', 'Kotak Sampah Proyek (Recycle Bin)')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0 text-dark font-weight-bold">
                <i class="fas fa-trash-restore text-danger mr-2"></i> Kotak Sampah Proyek (Recycle Bin)
            </h1>
            <p class="text-muted mb-0">Halaman pengelolaan data terhapus (Soft Deletes) - Hak Akses Eksklusif Admin.</p>
        </div>
        <a href="{{ route('project.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Proyek
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

    <div class="card card-danger card-outline shadow-sm">
        <div class="card-header">
            <h3 class="card-title font-weight-bold">
                <i class="fas fa-history mr-1 text-danger"></i> Arsip Proyek Terhapus
                <span class="badge badge-danger ml-2">Total: {{ $projects->total() }}</span>
            </h3>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Judul Proyek</th>
                            <th>Tema Agent</th>
                            <th>Pemilik</th>
                            <th>Waktu Penghapusan</th>
                            <th style="width: 140px;" class="text-center">Aksi Pemulihan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($projects as $project)
                            <tr>
                                <td class="font-weight-bold">{{ $project->judul }}</td>
                                <td>
                                    <span class="badge badge-secondary">{{ $project->tema_agent }}</span>
                                </td>
                                <td>
                                    <i class="fas fa-user-circle mr-1 text-secondary"></i>
                                    {{ $project->user->name ?? 'N/A' }}
                                </td>
                                <td class="text-danger small">
                                    <i class="fas fa-calendar-times mr-1"></i>
                                    {{ $project->deleted_at ? $project->deleted_at->format('d M Y H:i') : '-' }}
                                </td>
                                <td class="text-center">
                                    <form method="POST" action="{{ route('project.restore', $project) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Pulihkan proyek ini?')">
                                            <i class="fas fa-trash-restore mr-1"></i> Pulihkan
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    <i class="fas fa-check-circle fa-3x text-success d-block mb-3"></i>
                                    Kotak sampah kosong. Tidak ada data proyek yang terhapus sementara.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer d-flex justify-content-end">
            {{ $projects->links('pagination::bootstrap-4') }}
        </div>
    </div>
@stop
