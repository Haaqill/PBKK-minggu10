<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Proposal;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AdminController extends Controller implements HasMiddleware
{
    /**
     * Pengaman Controller berbasis Spatie Permission Middleware
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view-dashboard', only: ['index']),
            new Middleware('permission:delete-projects', only: ['nilai']),
        ];
    }

    public function index(): View
    {
        // Pengaman otorisasi manual server-side demi mencegah kebocoran URL
        abort_unless(
            auth()->user()->can('view-dashboard') || auth()->user()->hasAnyRole(['Admin', 'Staf', 'dosen', 'asdos']),
            403,
            'Anda tidak memiliki akses ke Dashboard Admin.'
        );

        // Widget Dashboard Agregat: Minimal 3 query model count()
        $totalProjects = Project::count();
        $totalProposals = Proposal::count();
        $totalUsers = User::count();
        $totalRecycleBin = Project::onlyTrashed()->count();

        // Data pelengkap untuk visual panel admin professional
        $recentProjects = Project::with('user:id,name')->latest()->take(5)->get();

        return view('admin.index', compact(
            'totalProjects',
            'totalProposals',
            'totalUsers',
            'totalRecycleBin',
            'recentProjects'
        ));
    }

    public function nilai(): View
    {
        // Hak Eksklusif Server-Side: Hanya Admin murni atau pemegang izin delete-projects
        abort_unless(
            auth()->user()->can('delete-projects') || auth()->user()->hasRole('Admin'),
            403,
            'Akses ditolak: Menu ini adalah hak eksklusif Admin.'
        );

        $proposals = Proposal::with('mahasiswa')->latest()->get();

        return view('admin.nilai', compact('proposals'));
    }
}
