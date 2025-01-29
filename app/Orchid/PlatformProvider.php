<?php

declare(strict_types=1);

namespace App\Orchid;

use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;
use Orchid\Support\Color;

class PlatformProvider extends OrchidServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @param Dashboard $dashboard
     *
     * @return void
     */
    public function boot(Dashboard $dashboard): void
    {
        parent::boot($dashboard);

        // ...
    }

    /**
     * Register the application menu.
     *
     * @return Menu[]
     */
    public function registerMainMenu(): array
    {
        return [
            Menu::make('Randevular')
                ->icon('calendar')
                ->route('platform.appointments')
                ->permission('platform.appointments')
                ->title('Randevu Yönetimi'),

            Menu::make('Zaman Dilimleri')
                ->icon('clock')
                ->route('platform.slots')
                ->permission('platform.slots'),

            Menu::make('Takımlar')
                ->icon('people')
                ->route('platform.teams')
                ->title('Takım Yönetimi')
                ->permission('platform.teams'),

            Menu::make('Yeni Takım')
                ->icon('plus')
                ->route('platform.teams.create')
                ->permission('platform.teams.create'),

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

    /**
     * Register permissions for the application.
     *
     * @return ItemPermission[]
     */
    public function registerPermissions(): array
    {
        return [
            ItemPermission::group(__('System'))
                ->addPermission('platform.systems.roles', __('Roles'))
                ->addPermission('platform.systems.users', __('Users')),

            ItemPermission::group('Randevu Yönetimi')
                ->addPermission('platform.appointments', 'Randevuları Görüntüle')
                ->addPermission('platform.appointments.create', 'Randevu Oluştur')
                ->addPermission('platform.appointments.edit', 'Randevu Düzenle')
                ->addPermission('platform.slots', 'Zaman Dilimlerini Görüntüle')
                ->addPermission('platform.slots.create', 'Zaman Dilimi Oluştur')
                ->addPermission('platform.slots.edit', 'Zaman Dilimi Düzenle'),

            ItemPermission::group('Takım Yönetimi')
                ->addPermission('platform.teams', 'Takımları Görüntüle')
                ->addPermission('platform.teams.create', 'Takım Oluştur')
                ->addPermission('platform.teams.edit', 'Takım Düzenle'),
        ];
    }
}
