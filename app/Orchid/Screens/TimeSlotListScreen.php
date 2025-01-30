<?php

namespace App\Orchid\Screens;

use App\Models\TimeSlot;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Carbon\Carbon;

class TimeSlotListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): array
    {
        return [
            'slots' => TimeSlot::with('category')
                ->filters()
                ->defaultSort('date')
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
        return 'Zaman Dilimleri';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.slots'
        ];
    }

    public function description(): ?string
    {
        return 'Randevu zaman dilimlerinin listesi';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): array
    {
        return [
            Link::make('Zaman Dilimi Ekle')
                ->icon('plus')
                ->route('platform.slots.create')
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
            Layout::table('slots', [
                TD::make('name', 'İsim')
                    ->sort()
                    ->filter(TD::FILTER_TEXT)
                    ->render(fn (TimeSlot $slot) => Link::make($slot->name)
                        ->route('platform.slots.edit', $slot)),

                TD::make('category.name', 'Kategori')
                    ->sort()
                    ->render(fn (TimeSlot $slot) => $slot->category ? $slot->category->name : '-'),

                TD::make('date', 'Tarih')
                    ->sort()
                    ->render(fn (TimeSlot $slot) => $slot->date ? $slot->date->format('d.m.Y') : '-'),

                TD::make('start_time', 'Başlangıç')
                    ->sort()
                    ->render(fn (TimeSlot $slot) => $slot->getStartTimeFormatted()),

                TD::make('end_time', 'Bitiş')
                    ->sort()
                    ->render(fn (TimeSlot $slot) => $slot->getEndTimeFormatted()),

                TD::make('max_appointments', 'Max. Randevu')
                    ->sort(),

                TD::make('is_active', 'Durum')
                    ->sort()
                    ->render(fn (TimeSlot $slot) => $slot->is_active ? 'Aktif' : 'Pasif'),
            ])
        ];
    }
}
