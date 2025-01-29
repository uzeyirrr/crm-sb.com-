<?php

namespace App\Orchid\Screens;

use App\Models\Category;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Illuminate\Support\Str;
use Tabuna\Breadcrumbs\Trail;

class CategoryEditScreen extends Screen
{
    public $category;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Category $category): array
    {
        $this->category = $category;
        
        return [
            'category' => $category
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->category->exists ? 'Kategori Düzenle' : 'Yeni Kategori';
    }

    public function description(): ?string
    {
        return 'Kategori bilgilerini düzenle';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.categories.edit'
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
            Trail::make('Kategoriler', route('platform.categories')),
            Trail::make($this->category->exists ? 'Kategori Düzenle' : 'Yeni Kategori'),
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
            Button::make(__('Save'))
                ->icon('check')
                ->method('createOrUpdate'),

            Button::make('Sil')
                ->icon('trash')
                ->method('remove')
                ->canSee($this->category->exists && $this->category->slug !== 'default')
                ->confirm('Bu kategoriyi silmek istediğinize emin misiniz?'),
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
                Input::make('category.name')
                    ->title('Kategori Adı')
                    ->required()
                    ->placeholder('Kategori adını giriniz'),

                TextArea::make('category.description')
                    ->title('Açıklama')
                    ->rows(3)
                    ->placeholder('Kategori açıklaması giriniz'),

                CheckBox::make('category.is_active')
                    ->title('Durum')
                    ->sendTrueOrFalse()
                    ->value($this->category->exists ? $this->category->is_active : true)
                    ->placeholder('Aktif')
                    ->help('Kategori pasif olduğunda, içindeki tüm slotlar da pasif olur.'),
            ])->title('Kategori Bilgileri')
        ];
    }

    /**
     * @param Category    $category
     * @param Request    $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createOrUpdate(Category $category, Request $request)
    {
        $validated = $request->validate([
            'category.name' => 'required|string|max:255',
            'category.description' => 'nullable|string',
            'category.is_active' => 'boolean',
        ]);

        $data = $validated['category'];
        
        // Varsayılan kategori için özel kontrol
        if ($category->exists && $category->slug === 'default') {
            $data['is_active'] = true; // Varsayılan kategori her zaman aktif olmalı
        }

        // Slug oluştur
        $baseSlug = Str::slug($data['name']);
        $slug = $baseSlug;
        $counter = 1;

        // Benzersiz slug oluştur
        $query = Category::where('slug', $slug);
        if ($category->exists) {
            $query->where('id', '!=', $category->id);
        }
        
        while ($query->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $query = Category::where('slug', $slug);
            if ($category->exists) {
                $query->where('id', '!=', $category->id);
            }
            $counter++;
        }

        $data['slug'] = $slug;

        // Yeni kayıt için is_active kontrolü
        if (!$category->exists) {
            $data['is_active'] = $data['is_active'] ?? true;
        }

        $category->fill($data)->save();

        Toast::success($category->wasRecentlyCreated ? 'Kategori oluşturuldu!' : 'Kategori güncellendi!');

        return redirect()->route('platform.categories');
    }

    /**
     * @param Category $category
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove(Category $category)
    {
        if ($category->slug === 'default') {
            Toast::error('Varsayılan kategori silinemez!');
            return redirect()->route('platform.categories');
        }

        $category->delete();

        Toast::success('Kategori başarıyla silindi!');

        return redirect()->route('platform.categories');
    }
}
