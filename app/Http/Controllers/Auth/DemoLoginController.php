<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DemoLoginController extends Controller
{
    public function login(Request $request)
    {
        abort_unless(app()->environment('local', 'testing'), 404);

        $validated = $request->validate([
            'role' => ['required', 'in:admin,staf,Admin,Staf,dosen,asdos,mahasiswa'],
        ]);

        $normalizedRole = strtolower($validated['role']);

        $email = match ($normalizedRole) {
            'admin' => 'admin@admin.com',
            'staf' => 'staf@staf.com',
            'dosen' => 'dosen@gmail.com',
            'asdos' => 'asdos@gmail.com',
            'mahasiswa' => 'hanif@gmail.com',
        };

        $user = User::query()
            ->where('email', $email)
            ->firstOrFail();

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route($user->dashboardRouteName());
    }
}
