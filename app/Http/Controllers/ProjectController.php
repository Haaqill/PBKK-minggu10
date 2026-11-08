<?php

namespace App\Http\Controllers;

use App\Http\Requests\BulkDeleteProjectsRequest;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Throwable;

class ProjectController extends Controller implements HasMiddleware
{
    /**
     * Pengaman Controller — Melampirkan constructor/controller middleware `permission:`
     * demi mencegah kebocoran URL direct access.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:delete-projects', only: ['bulkDestroy']),
        ];
    }

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Project::class);

        $isPrivileged = auth()->user()->hasAnyRole(['Admin', 'Staf', 'dosen', 'asdos'])
            || in_array(auth()->user()->role, ['Admin', 'Staf', 'dosen', 'asdos'], true);

        // Filter Pencarian dan Kueri when() — Mengembangkan form pencarian data di tabel admin
        // menggunakan query kondisional when() agar kodingan Controller tetap bersih.
        $projects = Project::query()
            ->with('user:id,name')
            ->when(! $isPrivileged, function ($query) {
                $query->whereBelongsTo(auth()->user());
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('judul', 'like', "%{$search}%")
                      ->orWhere('deskripsi', 'like', "%{$search}%")
                      ->orWhere('tema_agent', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('tema'), function ($query) use ($request) {
                $query->where('tema_agent', $request->input('tema'));
            })
            ->latest()
            // Pengunci Link Pagination — Mencegah hilangnya filter kata kunci saat berpindah halaman tabel data
            ->paginate(10)
            ->withQueryString();

        // Opsi pilihan tema unik untuk filter
        $availableThemes = Project::query()->distinct()->pluck('tema_agent')->filter()->values();

        return view('project.index', compact('projects', 'availableThemes'));
    }

    public function create(): View
    {
        Gate::authorize('create', Project::class);

        return view('project.create');
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $project = $this->transaction(function () use ($request): Project {
            $project = $request->user()->projects()->create($request->validated());
            $project->activityLogs()->create([
                'user_id' => $request->user()->id,
                'action' => 'created',
                'metadata' => ['judul' => $project->judul],
            ]);

            return $project;
        });

        return redirect()
            ->route('project.show', $project)
            ->with('success', 'Proyek berhasil dibuat.');
    }

    public function show(Project $project): View
    {
        Gate::authorize('view', $project);

        return view('project.show', compact('project'));
    }

    public function edit(Project $project): View
    {
        Gate::authorize('update', $project);

        return view('project.edit', compact('project'));
    }

    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $this->transaction(function () use ($request, $project): void {
            $data = $request->validated();
            if (blank($data['api_key_secure'] ?? null)) {
                unset($data['api_key_secure']);
            }

            $project->update($data);
            $project->activityLogs()->create([
                'user_id' => $request->user()->id,
                'action' => 'updated',
                'metadata' => ['judul' => $project->judul],
            ]);
        });

        return redirect()
            ->route('project.show', $project)
            ->with('success', 'Proyek berhasil diperbarui.');
    }

    /**
     * Hak Eksklusif — Menu tertentu seperti 'Hapus Data' hanya boleh dieksekusi di Controller
     * apabila pengguna memegang peran Admin murni.
     */
    public function destroy(Project $project): RedirectResponse
    {
        Gate::authorize('delete', $project);

        // Hak Eksklusif: Staf ditolak, hanya Admin murni yang berhak (atau mahasiswa pemilik)
        abort_if(
            auth()->user()->hasRole('Staf') || auth()->user()->role === 'Staf' || auth()->user()->role === 'asdos',
            403,
            'Akses ditolak: Staf tidak memiliki hak untuk menghapus data.'
        );

        abort_unless(
            auth()->user()->hasRole('Admin')
            || auth()->user()->can('delete-projects')
            || in_array(auth()->user()->role, ['Admin', 'dosen'], true)
            || ($project->user_id === auth()->id() && auth()->user()->role === 'mahasiswa'),
            403,
            'Akses ditolak: Hanya Admin murni yang berhak mengeksekusi penghapusan data.'
        );

        $this->transaction(function () use ($project): void {
            $project->delete();
            $project->activityLogs()->create([
                'user_id' => auth()->id(),
                'action' => 'deleted',
                'metadata' => ['judul' => $project->judul],
            ]);
        });

        return redirect()
            ->route('project.index')
            ->with('success', 'Proyek dipindahkan ke Recycle Bin.');
    }

    public function recycleBin(): View
    {
        Gate::authorize('viewAny', Project::class);

        abort_if(
            auth()->user()->hasRole('Staf') || auth()->user()->role === 'Staf' || auth()->user()->role === 'asdos',
            403,
            'Akses ditolak: Staf tidak memiliki hak untuk mengakses Recycle Bin.'
        );

        abort_unless(
            auth()->user()->hasRole('Admin')
            || auth()->user()->can('delete-projects')
            || in_array(auth()->user()->role, ['Admin', 'dosen'], true)
            || auth()->user()->role === 'mahasiswa',
            403,
            'Akses ditolak: Hanya Admin murni yang dapat mengakses Recycle Bin.'
        );

        $projects = Project::onlyTrashed()
            ->with('user:id,name')
            ->when(
                ! in_array(auth()->user()->role, ['Admin', 'dosen'], true) && ! auth()->user()->hasRole('Admin'),
                fn ($query) => $query->whereBelongsTo(auth()->user())
            )
            ->latest('deleted_at')
            ->paginate(15)
            ->withQueryString();

        return view('project.recycle-bin', compact('projects'));
    }

    public function restore(Project $project): RedirectResponse
    {
        abort_unless($project->trashed(), 404);
        Gate::authorize('restore', $project);

        abort_if(
            auth()->user()->hasRole('Staf') || auth()->user()->role === 'Staf' || auth()->user()->role === 'asdos',
            403,
            'Akses ditolak: Staf tidak memiliki hak untuk memulihkan data.'
        );

        abort_unless(
            auth()->user()->hasRole('Admin')
            || auth()->user()->can('delete-projects')
            || in_array(auth()->user()->role, ['Admin', 'dosen'], true)
            || ($project->user_id === auth()->id() && auth()->user()->role === 'mahasiswa'),
            403,
            'Akses ditolak: Hanya Admin murni yang dapat memulihkan data.'
        );

        $this->transaction(function () use ($project): void {
            $project->restore();
            $project->activityLogs()->create([
                'user_id' => auth()->id(),
                'action' => 'restored',
                'metadata' => ['judul' => $project->judul],
            ]);
        });

        return redirect()
            ->route('project.recycle-bin')
            ->with('success', 'Proyek berhasil dipulihkan.');
    }

    public function bulkDestroy(BulkDeleteProjectsRequest $request): RedirectResponse
    {
        // Otorisasi manual server-side
        abort_unless(
            auth()->user()->hasRole('Admin') || auth()->user()->can('delete-projects'),
            403,
            'Akses ditolak: Hanya Admin murni yang berhak mengeksekusi penghapusan massal.'
        );

        $this->transaction(function () use ($request): void {
            $projects = Project::query()
                ->whereKey($request->validated('project_ids'))
                ->get();

            foreach ($projects as $project) {
                $project->delete();
                $project->activityLogs()->create([
                    'user_id' => $request->user()->id,
                    'action' => 'bulk_deleted',
                    'metadata' => ['judul' => $project->judul],
                ]);
            }
        });

        return redirect()
            ->route('project.index')
            ->with('success', 'Proyek terpilih dipindahkan ke Recycle Bin.');
    }

    private function transaction(callable $operation): mixed
    {
        DB::beginTransaction();

        try {
            $result = $operation();
            DB::commit();

            return $result;
        } catch (Throwable $exception) {
            DB::rollBack();

            throw $exception;
        }
    }
}
