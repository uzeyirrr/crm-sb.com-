<?php

namespace App\Orchid\Screens;

use App\Models\TimeSlot;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class TimeSlotEditScreen extends Screen
{
    protected TimeSlot $slot;

    public function __construct()
    {
        $this->slot = new TimeSlot();
    }

    public function query(TimeSlot $slot = null): array
    {
        if ($slot->exists) {
            $this->slot = $slot;
        }
        
        return [
            'slot' => $this->slot
        ];
    }

    public function name(): ?string
    {
        return $this->slot->exists ? 'Zaman Dilimini Düzenle' : 'Yeni Zaman Dilimi';
    }

    public function permission(): ?iterable
    {
        return [
            $this->slot->exists ? 'platform.slots.edit' : 'platform.slots.create'
        ];
    }

    public function description(): ?string
    {
        return 'Zaman dilimi bilgilerini düzenle';
    }

    public function commandBar(): array
    {
        return [
            Button::make('Kaydet')
                ->icon('save')
                ->method('save'),

            Button::make('Sil')
                ->icon('trash')
                ->method('remove')
                ->canSee($this->slot->exists),
        ];
    }

    public function layout(): array
    {
        $hours = [];
        for ($i = 0; $i < 24; $i++) {
            $hour = str_pad($i, 2, '0', STR_PAD_LEFT);
            $hours[$hour.':00'] = $hour.':00';
        }

        return [
            Layout::rows([
                Input::make('slot.name')
                    ->title('İsim')
                    ->placeholder('Zaman dilimi adını girin')
                    ->required(),

                Select::make('slot.start_time')
                    ->title('Başlangıç Saati')
                    ->options($hours)
                    ->required(),

                Select::make('slot.end_time')
                    ->title('Bitiş Saati')
                    ->options($hours)
                    ->required(),

                Select::make('slot.interval_hours')
                    ->title('Randevu Aralığı')
                    ->options([
                        2 => '2 Saat',
                        3 => '3 Saat',
                        4 => '4 Saat',
                        6 => '6 Saat',
                    ])
                    ->help('Randevular arasındaki minimum süre')
                    ->required(),

                Input::make('slot.max_appointments')
                    ->type('number')
                    ->title('Maksimum Randevu Sayısı')
                    ->value(1)
                    ->min(1)
                    ->required(),

                CheckBox::make('slot.is_active')
                    ->title('Aktif')
                    ->value(1)
                    ->help('Bu zaman dilimi randevu almaya açık mı?'),

                TextArea::make('slot.description')
                    ->title('Açıklama')
                    ->rows(3)
                    ->placeholder('Zaman dilimi hakkında açıklama')
            ])
        ];
    }

    public function save(Request $request)
    {
        $data = $request->get('slot');
        
        $this->slot->fill($data)->save();

        Toast::info('Zaman dilimi başarıyla kaydedildi');

        return redirect()->route('platform.slots');
    }

    public function remove()
    {
        $this->slot->delete();

        Toast::info('Zaman dilimi başarıyla silindi');

        return redirect()->route('platform.slots');
    }
}
