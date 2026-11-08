@extends('adminlte::page')

@section('title', 'Daftar Proyek Agentic AI')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0 text-dark font-weight-bold">
                <i class="fas fa-folder-open text-primary mr-2"></i> Manajemen Proyek Agentic AI
            </h1>
            <p class="text-muted mb-0">Kelola dan telusuri portofolio proyek AI mahasiswa dan staf.</p>
        </div>
        <div>
            @can('delete-projects')
                <a href="{{ route('project.recycle-bin') }}" class="btn btn-outline-danger mr-2">
                    <i class="fas fa-trash-alt mr-1"></i> Kotak Sampah
                </a>
            @endcan
            <a href="{{ route('project.create') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus mr-1"></i> Buat Proyek Baru
            </a>
        </div>
    </div>
@stop

@section('content')
    {{-- Notifikasi Sukses / Error --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Form Pencarian Data & Filter Kueri when() --}}
    <div class="card card-default card-outline shadow-sm mb-4">
        <div class="card-header bg-light">
            <h3 class="card-title font-weight-bold text-secondary">
                <i class="fas fa-search mr-1"></i> Filter Pencarian Data
            </h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('project.index') }}" class="row align-items-end">
                <div class="col-md-5 mb-2">
                    <label for="search" class="form-label font-weight-bold">Kata Kunci Pencarian</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                        <input
                            type="search"
                            class="form-control"
                            id="search"
                            name="search"
                            placeholder="Cari berdasarkan judul, deskripsi, atau tema..."
                            value="{{ request('search') }}"
                        >
                    </div>
                </div>

                <div class="col-md-3 mb-2">
                    <label for="tema" class="form-label font-weight-bold">Filter Tema Agent</label>
                    <select name="tema" id="tema" class="form-control">
                        <option value="">-- Semua Tema Agent --</option>
                        @foreach ($availableThemes ?? ['Ollama', 'OpenAI', 'Gemini', 'Claude', 'DeepSeek'] as $temaOption)
                            <option value="{{ $temaOption }}" {{ request('tema') === $temaOption ? 'selected' : '' }}>
                                {{ $temaOption }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 mb-2">
                    <button type="submit" class="btn btn-primary mr-1">
                        <i class="fas fa-filter mr-1"></i> Terapkan Filter
                    </button>
                    <a href="{{ route('project.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-undo mr-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Data Proyek --}}
    <div class="card card-primary card-outline shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title font-weight-bold">
                <i class="fas fa-table mr-1 text-primary"></i> Data Proyek
                <span class="badge badge-info ml-2">Total: {{ $projects->total() }}</span>
            </h3>
            <div class="card-tools d-flex flex-wrap justify-content-end">
                @if(request('search'))
                    <span class="badge badge-light border mr-1 mb-1">
                        Pencarian: {{ request('search') }}
                    </span>
                @endif
                @if(request('tema'))
                    <span class="badge badge-light border mb-1">
                        Tema: {{ request('tema') }}
                    </span>
                @endif
            </div>
        </div>

        @can('delete-projects')
            <form method="POST" action="{{ route('project.bulk-destroy') }}" id="bulk-delete-form">
                @csrf
        @endcan

        <div class="card-body p-0">
            @error('project_ids')
                <div class="alert alert-danger m-3">{{ $message }}</div>
            @enderror

            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="thead-light">
                        <tr>
                            @can('delete-projects')
                                <th style="width: 40px;" class="text-center">
                                    <input type="checkbox" id="check-all" title="Pilih Semua">
                                </th>
                            @endcan
                            <th>Judul Proyek</th>
                            <th>Tema Agent</th>
                            <th>Pemilik</th>
                            <th>Waktu Pembuatan</th>
                            <th style="width: 220px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($projects as $project)
                            <tr>
                                @can('delete-projects')
                                    <td class="text-center">
                                        <input
                                            type="checkbox"
                                            name="project_ids[]"
                                            value="{{ $project->id }}"
                                            class="project-checkbox"
                                            aria-label="Pilih {{ $project->judul }}"
                                        >
                                    </td>
                                @endcan
                                <td class="font-weight-bold">
                                    {{ $project->judul }}
                                    <div class="text-muted small text-truncate" style="max-width: 450px;">
                                        {{ Str::limit($project->deskripsi, 90) }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-info px-2 py-1">{{ $project->tema_agent }}</span>
                                </td>
                                <td>
                                    <i class="fas fa-user-circle mr-1 text-secondary"></i>
                                    {{ $project->user->name ?? 'N/A' }}
                                </td>
                                <td class="text-muted small">
                                    {{ $project->created_at ? $project->created_at->format('d M Y H:i') : '-' }}
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('project.show', $project) }}" class="btn btn-outline-info" title="Lihat Detail">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                        <a href="{{ route('project.edit', $project) }}" class="btn btn-outline-secondary" title="Edit Proyek">
                                            <i class="fas fa-pencil-alt"></i> Ubah
                                        </a>

                                        {{-- Hak Eksklusif: Tombol Hapus Data HANYA dirender visual di Blade apabila memegang peran Admin murni --}}
                                        @can('delete-projects')
                                            <button
                                                type="button"
                                                class="btn btn-danger"
                                                title="Hapus Data (Hak Eksklusif Admin)"
                                                onclick="if(confirm('Yakin ingin memindahkan proyek ini ke Recycle Bin?')) { document.getElementById('delete-form-{{ $project->id }}').submit(); }"
                                            >
                                                <i class="fas fa-trash-alt"></i> Hapus
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-secondary disabled" title="Terkunci: Hanya Admin yang dapat menghapus" disabled>
                                                <i class="fas fa-lock"></i>
                                            </button>
                                        @endcan
                                    </div>

                                    @can('delete-projects')
                                        <form id="delete-form-{{ $project->id }}" action="{{ route('project.destroy', $project) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->can('delete-projects') ? 6 : 5 }}" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3 text-secondary d-block"></i>
                                    @if (request('search') || request('tema'))
                                        Tidak ada proyek yang cocok dengan filter saat ini.
                                    @else
                                        Belum ada proyek yang tersedia.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer d-flex justify-content-between align-items-center">
            <div>
                {{-- Tombol Bulk Delete: Hanya dirender untuk Admin --}}
                @can('delete-projects')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Pindahkan seluruh proyek terpilih ke Recycle Bin?')">
                        <i class="fas fa-trash-alt mr-1"></i> Hapus Proyek Terpilih
                    </button>
                @endcan
            </div>

            {{-- Pengunci Link Pagination --}}
            <div>
                {{ $projects->links('pagination::bootstrap-4') }}
            </div>
        </div>

        @can('delete-projects')
            </form>
        @endcan
    </div>
@stop

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkAll = document.getElementById('check-all');
            if (checkAll) {
                checkAll.addEventListener('change', function () {
                    const checkboxes = document.querySelectorAll('.project-checkbox');
                    checkboxes.forEach(cb => cb.checked = checkAll.checked);
                });
            }
        });
    </script>
@stop
