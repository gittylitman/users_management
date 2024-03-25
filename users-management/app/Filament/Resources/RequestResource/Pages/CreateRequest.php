<?php

namespace App\Filament\Resources\RequestResource\Pages;

use App\Filament\Resources\RequestResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\UserResource;

class CreateRequest extends CreateRecord
{
    protected static string $resource = RequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $username = UserResource::getUserFromAzure()->name;
        $data['submit_username'] = $username;
        $data['status'] = 'new';
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
