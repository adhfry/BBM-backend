<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * POST /api/v2/auth/register
     * Mendaftarkan akun pengguna baru via Cookie Session
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'student',
        ]);

        // Login otomatis menggunakan session cookie setelah register
        Auth::login($user);

        return apiSuccess(['user' => $user], 'Registrasi berhasil.', 201);
    }

    /**
     * POST /api/v2/auth/login
     * Autentikasi pengguna menggunakan SPA Cookie
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Autentikasi menggunakan session (bukan token text)
        if (Auth::attempt($credentials)) {
            // Regenerasi session untuk mencegah Session Fixation attack
            $request->session()->regenerate();

            return apiSuccess(['user' => Auth::user()], 'Login berhasil.');
        }

        throw ValidationException::withMessages([
            'email' => ['Kredensial yang Anda masukkan salah.'],
        ]);
    }

    /**
     * POST /api/v2/auth/logout
     * Menghapus sesi dan cookie user saat ini
     */
    public function logout(Request $request)
    {
        // Logout dari guard web (session)
        Auth::guard('web')->logout();

        // Invalidate session & regenerasi CSRF token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return apiSuccess([], 'Logout berhasil. Sesi telah diakhiri.');
    }

    /**
     * GET /api/v2/auth/me
     * Mengambil data profil user yang sedang login via Cookie
     */
    public function me(Request $request)
    {
        // logika kompleks untuk success dan error handling bisa ditambahkan disini jika diperlukan
        if (! $request->user()) {
            return apiError('Unauthorized', 401);
        }
        return apiSuccess(['user' => $request->user()], 'Berhasil mengambil data profil.');
    }
}
