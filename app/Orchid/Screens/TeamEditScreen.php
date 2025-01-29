<?php

namespace App\Orchid\Screens;

use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class TeamEditScreen extends Screen
{
    protected Team $team;

    public function __construct()
    {
        $this->team = new Team();
    }

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Team $team = null): array
    {
        if ($team->exists) {
            $this->team = $team;
        }
        
        return [
            'team' => $this->team
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->team->exists ? 'Takımı Düzenle' : 'Yeni Takım';
    }

    public function permission(): ?iterable
    {
        return [
            $this->team->exists ? 'platform.teams.edit' : 'platform.teams.create'
        ];
    }

    public function description(): ?string
    {
        return 'Takım bilgilerini düzenle';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): array
    {
        return [
            Button::make('Kaydet')
                ->icon('save')
                ->method('save'),

            Button::make('Sil')
                ->icon('trash')
                ->method('remove')
                ->canSee($this->team->exists),
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
            Layout::rows([
                Input::make('team.name')
                    ->title('Takım Adı')
                    ->placeholder('Takım adını girin')
                    ->required(),

                TextArea::make('team.description')
                    ->title('Açıklama')
                    ->rows(3)
                    ->placeholder('Takım açıklaması'),

                Relation::make('team.leader_id')
                    ->title('Takım Lideri')
                    ->fromModel(User::class, 'name')
                    ->required(),

                Relation::make('team.members.')
                    ->title('Takım Üyeleri')
                    ->fromModel(User::class, 'name')
                    ->multiple()
            ])
        ];
    }

    public function save(Request $request)
    {
        $this->team->fill($request->get('team'))->save();

        if ($request->input('team.members')) {
            $this->team->members()->sync($request->input('team.members'));
        }

        Toast::info('Takım başarıyla kaydedildi');

        return redirect()->route('platform.teams');
    }

    public function remove()
    {
        $this->team->delete();

        Toast::info('Takım başarıyla silindi');

        return redirect()->route('platform.teams');
    }
}
