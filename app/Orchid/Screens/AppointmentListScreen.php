<?php

namespace App\Orchid\Screens;

use App\Models\Appointment;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class AppointmentListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): array
    {
        return [
            'appointments' => Appointment::with(['timeSlot', 'creator'])
                ->filters()
                ->defaultSort('date', 'desc')
                ->paginate()
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Randevular';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.appointments'
        ];
    }

    public function description(): ?string
    {
        return 'Tüm randevuların listesi';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): array
    {
        return [
            Link::make('Randevu Ekle')
                ->icon('plus')
                ->route('platform.appointments.create')
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): array
    {
        return [
            Layout::table('appointments', [
                TD::make('first_name', 'İsim')
                    ->sort()
                    ->filter(TD::FILTER_TEXT)
                    ->render(fn (Appointment $appointment) => Link::make($appointment->first_name . ' ' . $appointment->last_name)
                        ->route('platform.appointments.edit', $appointment)),

                TD::make('phone', 'Telefon')
                    ->sort()
                    ->filter(TD::FILTER_TEXT),

                TD::make('date', 'Tarih')
                    ->sort()
                    ->render(fn (Appointment $appointment) => $appointment->date->format('d.m.Y')),

                TD::make('timeSlot.name', 'Zaman Dilimi')
                    ->sort(),

                TD::make('creator.name', 'Oluşturan')
                    ->sort()
                    ->render(fn (Appointment $appointment) => $appointment->creator ? $appointment->creator->name : '-'),

                TD::make('status', 'Durum')
                    ->sort()
                    ->render(fn (Appointment $appointment) => match($appointment->status) {
                        'pending' => 'Beklemede',
                        'confirmed' => 'Onaylandı',
                        'completed' => 'Tamamlandı',
                        'cancelled' => 'İptal Edildi',
                        default => $appointment->status
                    }),
            ])
        ];
    }
}
