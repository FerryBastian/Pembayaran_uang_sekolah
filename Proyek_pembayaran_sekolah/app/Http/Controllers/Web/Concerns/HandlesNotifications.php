<?php

namespace App\Http\Controllers\Web\Concerns;

use App\Models\Notifikasi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

trait HandlesNotifications
{
    public function index(Request $request): View
    {
        $notifikasis = $request->user()
            ->notifikasis()
            ->latest()
            ->paginate(10);

        $unreadCount = $request->user()
            ->notifikasis()
            ->where('status', 'belum_dibaca')
            ->count();

        return view('notifikasi.index', [
            'notifikasis' => $notifikasis,
            'unreadCount' => $unreadCount,
            'routePrefix' => $this->routePrefix($request),
            'roleLabel' => $this->roleLabel($request->user()->role),
        ]);
    }

    public function markAsRead(Request $request, Notifikasi $notifikasi): RedirectResponse
    {
        abort_unless($notifikasi->user_id === $request->user()->id, 403);

        $notifikasi->update(['status' => 'sudah_dibaca']);

        return redirect()
            ->route($this->routePrefix($request) . '.notifikasi.index')
            ->with('success', 'Notifikasi ditandai sudah dibaca.');
    }

    public function markAllAsRead(Request $request): RedirectResponse
    {
        $request->user()
            ->notifikasis()
            ->where('status', 'belum_dibaca')
            ->update(['status' => 'sudah_dibaca']);

        return redirect()
            ->route($this->routePrefix($request) . '.notifikasi.index')
            ->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }

    public function count(Request $request): JsonResponse
    {
        return response()->json([
            'count' => $request->user()
                ->notifikasis()
                ->where('status', 'belum_dibaca')
                ->count(),
        ]);
    }

    private function routePrefix(Request $request): string
    {
        return str($request->route()?->getName() ?? '')
            ->before('.')
            ->toString();
    }

    private function roleLabel(string $role): string
    {
        return [
            'admin' => 'Admin',
            'guru' => 'Guru',
            'orang_tua' => 'Orang Tua',
            'siswa' => 'Siswa',
        ][$role] ?? 'Pengguna';
    }
}
