<?php

namespace App\Http\Controllers\Web\Concerns;

use App\Models\OrangTua;
use App\Models\User;

trait ResolvesParentProfile
{
    private function resolveParentProfile(User $user, array $with = []): OrangTua
    {
        $orangTua = $user->orangTua()->firstOrCreate(
            ['user_id' => $user->id],
            [
                'nama' => $user->name,
                'no_hp' => '',
                'no_wa' => '',
                'alamat' => null,
            ]
        );

        if ($with !== []) {
            $orangTua->load($with);
        }

        return $orangTua;
    }
}
