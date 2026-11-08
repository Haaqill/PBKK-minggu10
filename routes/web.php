<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\DemoLoginController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

if (app()->environment('local', 'testing')) {
    Route::post('/_local/demo-login', [
        DemoLoginController::class,
        'login',
    ])->middleware('throttle:60,1')
      ->name('local.demo-login');
}

Route::get('/', [PageController::class, 'home']) ->name('home');

Route::get('/profil-mahasiswa/{nrp}', [PageController::class, 'profil']) 
    ->where('nrp', '[0-9]{10}')
    ->name('mahasiswa.profil');
    
Route::get('/ide-agent/{tema?}', [PageController::class, 'agent']) ->name('agent');

Route::get('/hitung-ipk/{ip1}/{ip2}', [PageController::class, 'hitung']) 
    ->where([
        'ip1' => '[0-4]+(\.[0-9]+)?',
        'ip2' => '[0-4]+(\.[0-9]+)?'
    ])
    ->name('hitung.ipk');

Route::fallback(function () {
    return response()->view('404', [], 404);    
});

Route::get('/dashboard', function () {
    return redirect()->route(auth()->user()->dashboardRouteName());
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::middleware('cek.peran:Admin,Staf,mahasiswa,dosen,asdos')->group(function () {
        Route::get('/project/recycle-bin', [ProjectController::class, 'recycleBin'])
            ->name('project.recycle-bin');

        Route::post('/project/{project}/restore', [ProjectController::class, 'restore'])
            ->withTrashed()
            ->name('project.restore');

        Route::resource('project', ProjectController::class);
    });

    Route::middleware(['auth', 'permission:delete-projects'])
        ->post('/project/bulk-delete', [ProjectController::class, 'bulkDestroy'])
        ->name('project.bulk-destroy');

    Route::middleware('cek.peran:mahasiswa')
        ->prefix('mahasiswa')
        ->name('mahasiswa.')
        ->group(function () {
            Route::get('/proposal', [ProposalController::class, 'index'])
                ->name('proposal.index');

            Route::get('/proposal/create', [ProposalController::class, 'create'])
                ->name('proposal.create');

            Route::post('/proposal', [ProposalController::class, 'store'])
                ->middleware('throttle:proposal-submit')
                ->name('proposal.store');

            Route::get('/proposal/{proposal}/edit', [ProposalController::class, 'edit'])
                ->name('proposal.edit');

            Route::put('/proposal/{proposal}', [ProposalController::class, 'update'])
                ->name('proposal.update');

            Route::delete('/proposal/{proposal}', [ProposalController::class, 'destroy'])
                ->name('proposal.destroy');
        });

    Route::middleware(['auth', 'verified', 'cek.peran:Admin,Staf,dosen,asdos'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::get('/', [AdminController::class, 'index'])
                ->name('index');

            Route::get('/proposal', [ProposalController::class, 'index'])
                ->name('proposal.index');

            Route::middleware('permission:delete-projects')->group(function () {
                Route::get('/nilai', [AdminController::class, 'nilai'])
                    ->name('nilai');
            });
        });

    Route::get('/proposal/{proposal}', [ProposalController::class, 'show'])
        ->name('proposal.show');

    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';