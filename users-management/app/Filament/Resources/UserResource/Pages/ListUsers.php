<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    public function mount(): void
    {
        $user = UserResource::getUserFromAzure();
        abort_unless($user->role === "Admin", 401);
    }

    public function getHeaderActions(): array   
    {
        return [
            // Actions\CreateAction::make(),
            Action::make('switchView')
                ->label(__('change view'))
                ->url(fn () =>request()->input('viewType', 'Table') === 'Table' ?  url()->current() . '?viewType=Card': url()->current() . '?viewType=Table'),
            Action::make(__('EXPORT'))
                ->label(__('Download'))
                ->url(fn () => route('requests.export'))
                ->icon('bi-download')
        ];
    }
}
