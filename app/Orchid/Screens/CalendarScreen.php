<?php

namespace App\Orchid\Screens;

use App\Models\Category;
use App\Models\TimeSlot;
use Carbon\Carbon;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Illuminate\Http\Request;

class CalendarScreen extends Screen
{
    protected $startOfWeek;
    protected $endOfWeek;

    public function __construct()
    {
        $this->startOfWeek = Carbon::now()->startOfWeek();
        $this->endOfWeek = Carbon::now()->endOfWeek();
    }

    public function query(Request $request): array
    {
        $selectedDate = $request->get('selected_date') ? Carbon::parse($request->get('selected_date')) : Carbon::now();
        $this->startOfWeek = $selectedDate->copy()->startOfWeek();
        $this->endOfWeek = $selectedDate->copy()->endOfWeek();

        $selectedCategory = $request->get('category_id');

        $slots = TimeSlot::with(['appointments', 'category'])
            ->where(function($query) {
                $query->whereBetween('date', [
                    $this->startOfWeek->format('Y-m-d'),
                    $this->endOfWeek->format('Y-m-d')
                ]);
            })
            ->when($selectedCategory, function($query) use ($selectedCategory) {
                return $query->where('category_id', $selectedCategory);
            })
            ->orderBy('start_time')
            ->get();

        // Debug için
        \Log::info('Slots Query:', [
            'start_date' => $this->startOfWeek->format('Y-m-d'),
            'end_date' => $this->endOfWeek->format('Y-m-d'),
            'slots_count' => $slots->count(),
            'raw_slots' => $slots->toArray()
        ]);

        // Slotları tarihe göre grupla
        $groupedSlots = $slots->groupBy(function($slot) {
            return $slot->date->format('Y-m-d');
        });

        return [
            'selected_date' => $selectedDate->format('Y-m-d'),
            'slots' => $groupedSlots,
            'week_dates' => collect()->range(0, 6)->map(function($day) {
                return $this->startOfWeek->copy()->addDays($day);
            }),
            'category_id' => $selectedCategory
        ];
    }

    public function name(): ?string
    {
        return 'Randevu Takvimi';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.calendar'
        ];
    }

    public function description(): ?string
    {
        return 'Haftalık randevu takvimi görünümü';
    }

    public function commandBar(): array
    {
        return [
            Link::make('Yeni Zaman Dilimi')
                ->icon('plus')
                ->route('platform.slots.create'),

            Button::make('Önceki Hafta')
                ->icon('arrow-left')
                ->method('navigateWeek', ['direction' => 'previous']),

            Button::make('Sonraki Hafta')
                ->icon('arrow-right')
                ->method('navigateWeek', ['direction' => 'next']),

            Button::make('Bugün')
                ->icon('calendar')
                ->method('navigateWeek', ['direction' => 'today']),
        ];
    }

    public function layout(): array
    {
        return [
            Layout::view('partials.calendar-filters', [
                'categories' => Category::all(),
                'selectedCategory' => request('category_id'),
                'selectedDate' => $this->startOfWeek->format('Y-m-d'),
            ]),

            Layout::view('calendar', [
                'slots' => $this->query(request())['slots'],
                'week_dates' => $this->query(request())['week_dates'],
            ]),
        ];
    }

    public function navigateWeek(Request $request, $direction)
    {
        $currentDate = Carbon::parse($request->get('selected_date', now()));
        
        switch ($direction) {
            case 'previous':
                $newDate = $currentDate->subWeek();
                break;
            case 'next':
                $newDate = $currentDate->addWeek();
                break;
            case 'today':
                $newDate = now();
                break;
            default:
                $newDate = $currentDate;
        }

        return redirect()->route('platform.calendar', [
            'selected_date' => $newDate->format('Y-m-d'),
            'category_id' => $request->get('category_id')
        ]);
    }
} 