<?php

namespace App\Orchid\Screens;

use App\Models\Team;
use App\Models\User;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Illuminate\Http\Request;
use Orchid\Support\Facades\Toast;

class TeamListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): array
    {
        return [
            'teams' => Team::with('leader')->paginate()
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Takımlar';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.teams'
        ];
    }

    /**
     * The screen's description.
     *
     * @return string|null
     */
    public function description(): ?string
    {
        return 'Tüm takımların listesi';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): array
    {
        return [
            Link::make('Takım Ekle')
                ->icon('plus')
                ->route('platform.teams.create'),
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
            \Orchid\Support\Facades\Layout::table('teams', [
                TD::make('name', 'Takım Adı')
                    ->sort()
                    ->filter(TD::FILTER_TEXT)
                    ->render(fn (Team $team) => Link::make($team->name)
                        ->route('platform.teams.edit', $team)),

                TD::make('leader.name', 'Takım Lideri')
                    ->sort()
                    ->render(fn (Team $team) => $team->leader->name),

                TD::make('members_count', 'Üye Sayısı')
                    ->render(fn (Team $team) => $team->members()->count()),

                TD::make('created_at', 'Oluşturulma Tarihi')
                    ->sort()
                    ->render(fn (Team $team) => $team->created_at->format('d.m.Y')),
            ]),
        ];
    }
}
