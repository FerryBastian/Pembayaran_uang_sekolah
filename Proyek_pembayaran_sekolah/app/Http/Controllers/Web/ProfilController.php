<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfilController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user()->load(['guru', 'siswa', 'orangTua']);

        return view('profil.edit', [
            'user' => $user,
            'profile' => $this->profileModel($user),
            'routePrefix' => $this->routePrefix($request),
            'roleLabel' => $this->roleLabel($user->role),
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'no_hp' => ['nullable', 'string', 'max:30'],
            'no_wa' => ['nullable', 'string', 'max:30'],
            'alamat' => ['nullable', 'string', 'max:1000'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
        ]);

        $profile = $this->profileModel($user->fresh(['guru', 'siswa', 'orangTua']));

        if ($profile) {
            $profileData = [
                'nama' => $validated['name'],
            ];

            if (array_key_exists('alamat', $profile->getAttributes())) {
                $profileData['alamat'] = $validated['alamat'] ?? null;
            }

            if ($user->role === 'orang_tua') {
                $profileData['no_hp'] = $validated['no_hp'] ?? '';
                $profileData['no_wa'] = $validated['no_wa'] ?? '';
            }

            $profile->update($profileData);
        }

        return redirect()
            ->route($this->routePrefix($request) . '.profil.edit')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if (!Hash::check($validated['current_password'], $request->user()->password)) {
            return back()
                ->withErrors(['current_password' => 'Password lama tidak sesuai.'])
                ->withInput();
        }

        $request->user()->update([
            'password' => $validated['password'],
        ]);

        return redirect()
            ->route($this->routePrefix($request) . '.profil.edit')
            ->with('success', 'Password berhasil diperbarui.');
    }

    private function profileModel($user)
    {
        return match ($user->role) {
            'guru' => $user->guru,
            'siswa' => $user->siswa,
            'orang_tua' => $user->orangTua,
            default => null,
        };
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
