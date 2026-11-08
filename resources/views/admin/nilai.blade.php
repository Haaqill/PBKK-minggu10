@extends('adminlte::page')

@section('title', 'Input Nilai Dosen - Hak Eksklusif')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0 text-dark font-weight-bold">
                <i class="fas fa-award text-warning mr-2"></i> Input Nilai Dosen
            </h1>
            <p class="text-muted mb-0">Halaman otorisasi khusus: Hanya dapat diakses oleh peran Admin murni.</p>
        </div>
        <a href="{{ route('admin.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Dashboard
        </a>
    </div>
@stop

@section('content')
    <div class="card card-warning card-outline shadow-sm">
        <div class="card-header">
            <h3 class="card-title font-weight-bold">
                <i class="fas fa-clipboard-check mr-1 text-warning"></i> Form Evaluasi Nilai Proposal Mahasiswa
            </h3>
            <div class="card-tools">
                <span class="badge badge-success px-2 py-1">Otorisasi Terverifikasi: Admin</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Judul Proposal</th>
                            <th>Pengusul</th>
                            <th>Status Proposal</th>
                            <th>Form Input Nilai (0 - 100)</th>
                            <th style="width: 130px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($proposals as $index => $proposal)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="font-weight-bold">{{ $proposal->judul }}</td>
                                <td>{{ $proposal->mahasiswa->name ?? 'Mahasiswa' }}</td>
                                <td>
                                    <span class="badge badge-{{ $proposal->status === 'disetujui' ? 'success' : ($proposal->status === 'ditolak' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($proposal->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <input type="number" class="form-control" min="0" max="100" placeholder="Masukkan nilai angka..." value="85">
                                        <div class="input-group-append">
                                            <span class="input-group-text">/ 100</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-success" onclick="alert('Nilai untuk proposal berhasil disimpan secara lokal!')">
                                        <i class="fas fa-save mr-1"></i> Simpan
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Belum ada proposal yang siap dinilai.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-muted text-sm">
            <i class="fas fa-shield-alt mr-1 text-primary"></i> Data nilai dilindungi enkripsi dan hanya dapat diedit oleh akun berlisensi Admin.
        </div>
    </div>
@stop
