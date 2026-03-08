<div class="space-y-6">
    <!-- Analytics Overview Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-blue-50 p-4 rounded-lg text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $analytics['total_bookings'] }}</div>
            <div class="text-sm text-blue-600">Total Booking</div>
        </div>

        <div class="bg-yellow-50 p-4 rounded-lg text-center">
            <div class="text-2xl font-bold text-yellow-600">{{ $analytics['pending_bookings'] }}</div>
            <div class="text-sm text-yellow-600">Pending</div>
        </div>

        <div class="bg-green-50 p-4 rounded-lg text-center">
            <div class="text-2xl font-bold text-green-600">{{ $analytics['completed_bookings'] }}</div>
            <div class="text-sm text-green-600">Selesai</div>
        </div>

        <div class="bg-purple-50 p-4 rounded-lg text-center">
            <div class="text-2xl font-bold text-purple-600">{{ $analytics['popularity_score'] }}</div>
            <div class="text-sm text-purple-600">Skor Popularitas</div>
        </div>
    </div>

    <!-- Product Info -->
    <div class="bg-gray-50 p-4 rounded-lg">
        <h3 class="font-semibold text-lg mb-3">Informasi Produk</h3>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <label class="font-medium text-gray-600">Nama:</label>
                <span class="ml-2">{{ $record->name }}</span>
            </div>
            <div>
                <label class="font-medium text-gray-600">Kategori:</label>
                <span class="ml-2">{{ $record->category }}</span>
            </div>
            <div>
                <label class="font-medium text-gray-600">Tipe:</label>
                <span class="ml-2">{{ $record->type }}</span>
            </div>
            <div>
                <label class="font-medium text-gray-600">Harga:</label>
                <span class="ml-2">Rp {{ number_format($record->price, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- Recent Trend (Last 7 Days) -->
    <div>
        <h3 class="font-semibold text-lg mb-3">Trend Booking (7 Hari Terakhir)</h3>
        <div class="bg-gray-50 p-4 rounded-lg">
            @if(array_sum($trend) > 0)
                <div class="grid grid-cols-7 gap-1 text-center text-xs">
                    @foreach($trend as $date => $count)
                        <div class="flex flex-col items-center p-2">
                            <div
                                class="w-8 h-8 rounded {{ $count > 0 ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-500' }} flex items-center justify-center font-bold">
                                {{ $count }}
                            </div>
                            <div class="mt-1 text-gray-600">{{ \Carbon\Carbon::parse($date)->format('d/m') }}</div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-3 text-sm text-gray-600">
                    <strong>Rata-rata per hari:</strong> {{ $analytics['average_per_day'] }} booking
                </div>
            @else
                <div class="text-center text-gray-500 py-4">
                    Tidak ada booking dalam 7 hari terakhir
                </div>
            @endif
        </div>
    </div>

    <!-- Status & Availability -->
    <div>
        <h3 class="font-semibold text-lg mb-3">Status & Ketersediaan</h3>
        <div class="bg-gray-50 p-4 rounded-lg">
            <div class="flex items-center justify-between text-sm">
                <div>
                    <label class="font-medium text-gray-600">Status Produk:</label>
                    <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                        {{ $record->is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $record->is_available ? 'Tersedia' : 'Tidak Tersedia' }}
                    </span>
                </div>

                @if($analytics['pending_bookings'] > 0)
                    <div>
                        <span
                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            ⚠️ {{ $analytics['pending_bookings'] }} meeting menunggu konfirmasi
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end space-x-3 pt-4 border-t">
        <button type="button"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200"
            onclick="window.open('/admin/produks/{{ $record->id }}', '_blank')">
            👁️ Lihat Detail Produk
        </button>

        @if($analytics['pending_bookings'] > 0)
            <button type="button"
                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700"
                onclick="window.open('/admin/meeting-requests?product={{ $record->id }}', '_blank')">
                📅 Kelola Meeting Requests
            </button>
        @endif
    </div>
</div>