<div class="bg-white rounded-lg shadow">
    <!-- Hafta Başlıkları -->
    <div class="grid grid-cols-7 border-b">
        @foreach(['Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi', 'Pazar'] as $day)
            <div class="p-4 text-center border-r last:border-r-0">
                <span class="font-semibold text-gray-900">{{ $day }}</span>
            </div>
        @endforeach
    </div>

    <!-- Takvim İçeriği -->
    <div class="grid grid-cols-7">
        @foreach($week_dates as $date)
            <div class="min-h-[200px] p-4 border-r border-b last:border-r-0 relative group hover:bg-gray-50">
                <!-- Tarih -->
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-medium text-gray-900">{{ $date->format('d.m.Y') }}</span>
                    
                    <!-- Yeni Slot Ekleme Butonu -->
                    <a href="{{ route('platform.slots.create', ['date' => $date->format('Y-m-d')]) }}" 
                       class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 text-green-600 hover:text-green-800">
                        <i class="icon-plus"></i>
                    </a>
                </div>

                <!-- Slotlar -->
                @if(isset($slots[$date->format('Y-m-d')]))
                    @foreach($slots[$date->format('Y-m-d')] as $slot)
                        <div class="mb-3 p-3 rounded-lg {{ $slot->is_active ? 'bg-white' : 'bg-gray-100' }} border shadow-sm hover:shadow-md transition-shadow duration-200">
                            <!-- Slot Başlığı -->
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-medium text-gray-900">{{ $slot->name }}</span>
                                <span class="text-xs px-2 py-1 rounded-full {{ $slot->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $slot->is_active ? 'Aktif' : 'Pasif' }}
                                </span>
                            </div>

                            <!-- Slot Detayları -->
                            <div class="text-sm text-gray-600 mb-2">
                                <div class="flex items-center gap-2">
                                    <i class="icon-clock"></i>
                                    {{ $slot->getStartTimeFormatted() }} - {{ $slot->getEndTimeFormatted() }}
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="icon-folder"></i>
                                    {{ $slot->category->name }}
                                </div>
                            </div>

                            <!-- Randevu Durumları -->
                            <div class="flex flex-wrap gap-1 mt-2">
                                @for($i = 0; $i < $slot->max_appointments; $i++)
                                    @php
                                        $appointment = $slot->appointments->where('status', '!=', 'cancelled')->values()->get($i);
                                    @endphp

                                    @if($appointment)
                                        <div class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-800 flex items-center gap-1" 
                                             title="{{ $appointment->first_name }} {{ $appointment->last_name }}">
                                            <i class="icon-user"></i>
                                            {{ Str::limit($appointment->first_name . ' ' . $appointment->last_name, 15) }}
                                        </div>
                                    @else
                                        <a href="{{ route('platform.appointments.create', ['time_slot_id' => $slot->id]) }}" 
                                           class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-800 hover:bg-green-200 flex items-center gap-1">
                                            <i class="icon-plus"></i>
                                            Boş
                                        </a>
                                    @endif
                                @endfor
                            </div>

                            <!-- Düzenleme Linki -->
                            <a href="{{ route('platform.slots.edit', $slot) }}" 
                               class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200 text-blue-600 hover:text-blue-800">
                                <i class="icon-pencil"></i>
                            </a>
                        </div>
                    @endforeach
                @endif
            </div>
        @endforeach
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Randevu oluşturma linklerine tıklandığında kontrol et
    document.querySelectorAll('a[href*="appointments/create"]').forEach(link => {
        link.addEventListener('click', async function(e) {
            e.preventDefault();
            
            const timeSlotId = new URLSearchParams(this.href.split('?')[1]).get('time_slot_id');
            
            try {
                const response = await fetch(`/api/check-slot-availability/${timeSlotId}`);
                const data = await response.json();
                
                if (data.available) {
                    window.location.href = this.href;
                } else {
                    alert('Bu slot başka bir kullanıcı tarafından seçilmiş olabilir. Lütfen sayfayı yenileyin.');
                    window.location.reload();
                }
            } catch (error) {
                console.error('Slot kontrolü sırasında hata:', error);
                alert('Bir hata oluştu. Lütfen tekrar deneyin.');
            }
        });
    });
});
</script>
@endpush 