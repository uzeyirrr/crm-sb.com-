<?php

namespace App\Orchid\Screens;

use App\Models\TimeSlot;
use App\Models\Category;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Carbon\Carbon;

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
        } else {
            // URL'den tarih parametresi varsa onu kullan
            $date = request('date') ? Carbon::parse(request('date')) : now();
            $this->slot->date = $date;
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

                Select::make('slot.category_id')
                    ->title('Kategori')
                    ->fromModel(Category::class, 'name')
                    ->required()
                    ->help('Bu zaman diliminin ait olduğu kategori'),

                DateTimer::make('slot.date')
                    ->title('Tarih')
                    ->format('Y-m-d')
                    ->required()
                    ->value($this->slot->exists ? $this->slot->date : now())
                    ->help('Bu zaman diliminin tarihi'),

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
        $data = $request->validate([
            'slot.name' => 'required|string',
            'slot.category_id' => 'required|exists:categories,id',
            'slot.date' => 'required|date',
            'slot.start_time' => 'required',
            'slot.end_time' => 'required',
            'slot.interval_hours' => 'required|integer',
            'slot.max_appointments' => 'required|integer|min:1',
            'slot.is_active' => 'boolean',
            'slot.description' => 'nullable|string',
        ]);

        $slotData = $data['slot'];
        
        // Önce tarihi ayarla
        $this->slot->date = Carbon::parse($slotData['date']);
        
        // Sonra diğer alanları doldur
        $this->slot->fill($slotData);
        
        // Kaydet
        $this->slot->save();

        Toast::info('Zaman dilimi başarıyla kaydedildi');

        return redirect()->route('platform.calendar', [
            'selected_date' => $this->slot->date->format('Y-m-d')
        ]);
    }

    public function remove()
    {
        $this->slot->delete();

        Toast::info('Zaman dilimi başarıyla silindi');

        return redirect()->route('platform.slots');
    }
}
