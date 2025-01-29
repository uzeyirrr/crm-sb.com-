<?php

namespace App\Orchid\Screens;

use App\Models\Category;
use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Toast;
use Tabuna\Breadcrumbs\Trail;

class CategoryListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): array
    {
        return [
            'categories' => Category::filters()
                ->defaultSort('id', 'desc')
                ->paginate(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Kategoriler';
    }

    public function description(): ?string
    {
        return 'Tüm kategorilerin listesi';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.categories'
        ];
    }

    /**
     * Get the breadcrumbs for the screen.
     *
     * @return \Tabuna\Breadcrumbs\Trail[]
     */
    public function breadcrumbs(): array
    {
        return [
            Trail::make('Kategoriler', route('platform.categories'))
        ];
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): array
    {
        return [
            Link::make('Kategori Ekle')
                ->icon('plus')
                ->route('platform.categories.create')
                ->permission('platform.categories.create'),
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
            Layout::table('categories', [
                TD::make('id', 'ID')
                    ->sort()
                    ->filter(TD::FILTER_NUMERIC),

                TD::make('name', 'Kategori Adı')
                    ->sort()
                    ->filter(TD::FILTER_TEXT)
                    ->render(fn (Category $category) => 
                        Link::make($category->name)
                            ->route('platform.categories.edit', $category)
                            ->permission('platform.categories.edit')
                    ),

                TD::make('is_active', 'Durum')
                    ->sort()
                    ->render(fn (Category $category) => 
                        $category->is_active 
                            ? '<span class="text-success">Aktif</span>' 
                            : '<span class="text-danger">Pasif</span>'
                    ),

                TD::make('time_slots_count', 'Slot Sayısı')
                    ->render(fn (Category $category) => 
                        $category->timeSlots()->count()
                    ),

                TD::make('created_at', 'Oluşturulma Tarihi')
                    ->sort()
                    ->render(fn (Category $category) => 
                        $category->created_at->format('d.m.Y H:i')
                    ),

                TD::make('actions', 'İşlemler')
                    ->align(TD::ALIGN_CENTER)
                    ->width('200px')
                    ->render(function (Category $category) {
                        $buttons = [];

                        if (auth()->user()->hasAccess('platform.categories.edit')) {
                            $buttons[] = Link::make('Düzenle')
                                ->icon('pencil')
                                ->route('platform.categories.edit', $category);
                        }

                        if (auth()->user()->hasAccess('platform.categories.delete') && $category->slug !== 'default') {
                            $buttons[] = Button::make('Sil')
                                ->icon('trash')
                                ->confirm('Bu kategoriyi silmek istediğinize emin misiniz?')
                                ->method('remove', ['id' => $category->id]);
                        }

                        return implode(' ', array_map(fn($button) => $button->render(), $buttons));
                    }),
            ])
        ];
    }

    public function remove(Request $request): void
    {
        $category = Category::findOrFail($request->get('id'));

        if ($category->slug === 'default') {
            Toast::error('Varsayılan kategori silinemez!');
            return;
        }

        $category->delete();
        Toast::success('Kategori başarıyla silindi!');
    }
}
