<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProposalController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Proposal::class);

        $proposals = Proposal::query()
            ->with('mahasiswa')
            ->when(
                $request->user()->role === 'mahasiswa',
                fn ($query) => $query->where('user_id', $request->user()->id)
            )
            ->latest()
            ->get();

        if ($request->routeIs('admin.*') || $request->is('admin*')) {
            return view('admin.proposal.index', compact('proposals'));
        }

        return view('proposal.index', compact('proposals'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Proposal::class);

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string', 'max:5000'],
        ]);

        $request->user()->proposals()->create($validated);

        return redirect()
            ->route('mahasiswa.proposal.index')
            ->with('success', 'Proposal berhasil diajukan.');
    }

    public function show(Proposal $proposal)
    {
        Gate::authorize('view', $proposal);

        return view('proposal.show', compact('proposal'));
    }

    public function create()
    {
        Gate::authorize('create', Proposal::class);

        return view('proposal.create');
    }

    public function edit(Proposal $proposal)
    {
        Gate::authorize('update', $proposal);

        return view('proposal.edit', compact('proposal'));
    }

    public function update(
        Request $request,
        Proposal $proposal
    ) {
        Gate::authorize('update', $proposal);

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string', 'max:5000'],
        ]);

        $proposal->update($validated);

        return redirect()
            ->route('mahasiswa.proposal.index')
            ->with('success', 'Proposal berhasil diperbarui.');
    }

    public function destroy(Proposal $proposal)
    {
        Gate::authorize('delete', $proposal);

        $proposal->delete();

        return redirect()
            ->route('mahasiswa.proposal.index')
            ->with('success', 'Proposal berhasil dihapus.');
    }
}