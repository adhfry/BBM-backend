<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    /**
     * GET /api/v2/public/about
     */
    public function about()
    {
        $data = [
            'app_name'    => config('app.name'),
            'version'     => config('app.version'),
            'description' => config('app.description'),
            'author'      => config('app.author'),
        ];
        return apiSuccess($data, 'Berhasil mengambil informasi aplikasi.');
    }

    /**
     * POST /api/v2/public/contact
     */
    public function contact(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:100',
            'message' => 'required|string|max:1000',
        ]);
        // logika disini

        return apiSuccess(null, 'Pesan Anda berhasil dikirim. Terima kasih!');
    }
}
