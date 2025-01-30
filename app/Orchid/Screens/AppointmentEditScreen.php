<?php

namespace App\Orchid\Screens;

use App\Models\Appointment;
use App\Models\TimeSlot;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Fields\Map;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class AppointmentEditScreen extends Screen
{
    protected Appointment $appointment;

    public function __construct()
    {
        $this->appointment = new Appointment();
    }

    public function query(Appointment $appointment = null): array
    {
        if ($appointment->exists) {
            $this->appointment = $appointment;
        }
        
        return [
            'appointment' => $this->appointment,
            'creator' => auth()->user()->name
        ];
    }

    public function name(): ?string
    {
        return $this->appointment->exists ? 'Randevuyu Düzenle' : 'Yeni Randevu';
    }

    public function permission(): ?iterable
    {
        return [
            $this->appointment->exists ? 'platform.appointments.edit' : 'platform.appointments.create'
        ];
    }

    public function description(): ?string
    {
        return 'Randevu bilgilerini düzenle';
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
                ->canSee($this->appointment->exists),
        ];
    }

    public function layout(): array
    {
        return [
            Layout::tabs([
                'Temel Bilgiler' => [
                    Layout::rows([
                        Input::make('appointment.first_name')
                            ->title('İsim')
                            ->required(),

                        Input::make('appointment.last_name')
                            ->title('Soyisim')
                            ->required(),

                        Input::make('appointment.phone')
                            ->title('Telefon')
                            ->mask('+90 (999) 999-9999')
                            ->required(),

                        Input::make('appointment.email')
                            ->title('E-posta')
                            ->type('email')
                            ->required(),

                        Select::make('appointment.contact_gender')
                            ->title('Kimle Konuşuldu')
                            ->options([
                                'female' => 'Kadın',
                                'male' => 'Erkek'
                            ])
                            ->required(),

                        Input::make('appointment.household_size')
                            ->title('Evde Kaç Kişi Yaşıyor')
                            ->type('number')
                            ->min(1)
                            ->required(),

                        Input::make('appointment.customer_age')
                            ->title('Müşteri Yaşı')
                            ->type('number')
                            ->min(18),
                    ])
                ],
                'Konum Bilgileri' => [
                    Layout::rows([
                        Map::make('appointment.latitude', 'appointment.longitude')
                            ->title('Konum')
                            ->help('Haritadan konum seçin'),

                        Input::make('appointment.street')
                            ->title('Sokak')
                            ->placeholder('Sokak adı ve numara'),

                        Input::make('appointment.city')
                            ->title('Şehir')
                            ->required(),

                        Input::make('appointment.postal_code')
                            ->title('Posta Kodu')
                            ->mask('99999')
                            ->required(),
                    ])
                ],
                'Randevu Detayları' => [
                    Layout::rows([
                        Select::make('appointment.time_slot_id')
                            ->title('Zaman Dilimi')
                            ->fromModel(TimeSlot::class, 'name')
                            ->required()
                            ->help('Seçilen zaman dilimine göre randevu tarihi otomatik belirlenecektir'),

                        Input::make('creator')
                            ->title('Oluşturan')
                            ->readonly()
                            ->canSee($this->appointment->exists),

                        Input::make('appointment.monthly_electricity_usage')
                            ->title('Aylık Elektrik Tüketimi (kWh)')
                            ->type('number')
                            ->step(0.01),

                        Select::make('appointment.status')
                            ->title('Randevu Durumu')
                            ->options([
                                'pending' => 'Beklemede',
                                'confirmed' => 'Onaylandı',
                                'completed' => 'Tamamlandı',
                                'cancelled' => 'İptal Edildi'
                            ])
                            ->required(),

                        TextArea::make('appointment.customer_notes')
                            ->title('Müşteri ile Konuşulanlar')
                            ->rows(5),

                        Upload::make('appointment.roof_image')
                            ->title('Çatının Resmi')
                            ->maxFiles(1)
                            ->acceptedFiles('image/*')
                            ->help('Çatının fotoğrafını yükleyin'),
                    ])
                ]
            ])
        ];
    }

    public function save(Request $request)
    {
        $data = $request->get('appointment');
        
        if (!$this->appointment->exists) {
            $data['creator_id'] = auth()->id();
            
            // Seçilen zaman diliminin tarihini al
            $timeSlot = TimeSlot::findOrFail($data['time_slot_id']);
            $data['date'] = $timeSlot->date;
        }
        
        $this->appointment->fill($data)->save();

        Toast::info('Randevu başarıyla kaydedildi');

        return redirect()->route('platform.appointments');
    }

    public function remove()
    {
        $this->appointment->delete();

        Toast::info('Randevu silindi');

        return redirect()->route('platform.appointments');
    }
}
