<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['password'] = bcrypt('password'); // ✅ Fixes NOT NULL error
      //  $data['role'] = 'user';                 // ✅ Default role

        return $data;
    }
}
