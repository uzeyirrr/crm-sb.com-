<?php

namespace App\Providers;

use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;

class PlatformServiceProvider extends OrchidServiceProvider
{
    public function boot(Dashboard $dashboard): void
    {
        parent::boot($dashboard);
    }

    public function registerMainMenu(): array
    {
        return [
            Menu::make('Takımlar')
                ->icon('people')
                ->route('platform.teams')
                ->title('Takım Yönetimi'),

            Menu::make('Yeni Takım')
                ->icon('plus')
                ->route('platform.teams.create')
                ->title('Takım Yönetimi'),

            Menu::make(__('Users'))
                ->icon('user')
                ->route('platform.systems.users')
                ->permission('platform.systems.users')
                ->title(__('Access rights')),

            Menu::make(__('Roles'))
                ->icon('lock')
                ->route('platform.systems.roles')
                ->permission('platform.systems.roles'),
        ];
    }

    public function registerPermissions(): array
    {
        return [
            ItemPermission::group(__('System'))
                ->addPermission('platform.systems.roles', __('Roles'))
                ->addPermission('platform.systems.users', __('Users')),

            ItemPermission::group('Takım Yönetimi')
                ->addPermission('platform.teams', 'Takımları Görüntüle')
                ->addPermission('platform.teams.create', 'Takım Oluştur')
                ->addPermission('platform.teams.edit', 'Takım Düzenle'),
        ];
    }
} 