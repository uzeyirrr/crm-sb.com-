<div class="bg-white p-4 rounded-lg shadow mb-4">
    <div class="flex flex-wrap items-center gap-4">
        <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
            <select name="category_id" class="form-control w-full rounded-md border-gray-300" 
                    onchange="this.form.submit()">
                <option value="">Tüm Kategoriler</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" 
                            {{ $selectedCategory == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 mb-1">Tarih</label>
            <input type="date" name="selected_date" 
                   value="{{ $selectedDate }}"
                   class="form-control w-full rounded-md border-gray-300"
                   onchange="this.form.submit()">
        </div>
    </div>
</div> 