<div class="w-full">
    <div class="w-full rounded-2xl overflow-hidden bg-white dark:bg-gray-900 border-2 border-gray-200 dark:border-gray-700 shadow-xl"
        style="min-height: 400px;">

        {{-- HERO IMAGE --}}
        <div
            class="relative w-full h-[240px] bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-900">
            @if($image)
                <img src="{{ Storage::url($image) }}" alt="{{ $name }}" class="absolute inset-0 w-full h-full object-cover"
                    loading="lazy" onerror="this.src='{{ url('/images/placeholder.png') }}'">

                {{-- Overlay Gradient --}}
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
            @else
                <div class="absolute inset-0 flex flex-col items-center justify-center gap-3">
                    <svg class="w-20 h-20 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400 font-medium">Gambar tidak tersedia</p>
                </div>
            @endif

            {{-- STATUS BADGE --}}
            <div class="absolute top-6 right-6">
                @if($is_available)
                    <span
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-sm font-bold bg-gradient-to-r from-emerald-500 to-emerald-600 text-white shadow-lg backdrop-blur-sm">
                        <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span>
                        Tersedia
                    </span>
                @else
                    <span
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-sm font-bold bg-gradient-to-r from-red-500 to-red-600 text-white shadow-lg backdrop-blur-sm">
                        <span class="w-2 h-2 bg-white rounded-full"></span>
                        Tidak Tersedia
                    </span>
                @endif
            </div>
        </div>

        {{-- CONTENT --}}
        <div class="p-6 space-y-5">

            {{-- TITLE & TAGS --}}
            <div class="space-y-3">
                <h1 class="text-3xl font-black text-gray-900 dark:text-white leading-tight tracking-tight">
                    {{ $name }}
                </h1>

                <div class="flex flex-wrap gap-2">
                    <span
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/30 border border-blue-200 dark:border-blue-700/50 text-blue-700 dark:text-blue-300 text-sm font-semibold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                            </path>
                        </svg>
                        {{ ucfirst($category) }}
                    </span>
                    <span
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/30 dark:to-purple-800/30 border border-purple-200 dark:border-purple-700/50 text-purple-700 dark:text-purple-300 text-sm font-semibold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        {{ ucfirst($type) }}
                    </span>
                </div>
            </div>

            {{-- PRICE CARD --}}
            <div
                class="relative overflow-hidden p-6 rounded-xl bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-600 shadow-lg">
                {{-- Decorative Background --}}
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>

                <div class="relative">
                    <p class="text-xs font-medium text-white/80 mb-1 tracking-wide uppercase">Harga Produk</p>
                    <p class="text-3xl font-black text-white tracking-tight">
                        Rp {{ number_format($price, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            {{-- DESCRIPTION --}}
            <div class="space-y-2.5">
                <div class="flex items-center gap-2">
                    <div class="flex-shrink-0 w-1 h-6 bg-gradient-to-b from-blue-600 to-indigo-600 rounded-full"></div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        Deskripsi Produk
                    </h3>
                </div>

                <div
                    class="p-5 rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700/50">
                    <div
                        class="prose prose-sm max-w-none dark:prose-invert prose-headings:font-bold prose-p:text-gray-700 dark:prose-p:text-gray-300 prose-a:text-blue-600 dark:prose-a:text-blue-400 prose-strong:text-gray-900 dark:prose-strong:text-white leading-relaxed">
                        {!! \Illuminate\Support\Str::markdown($description ?? '_Tidak ada deskripsi tersedia untuk produk ini._') !!}
                    </div>
                </div>
            </div>

            {{-- METADATA FOOTER --}}
            <div class="pt-5 mt-2 border-t-2 border-gray-200 dark:border-gray-700/60">
                <div class="grid grid-cols-2 gap-6">
                    <div class="flex items-start gap-2">
                        <div
                            class="flex-shrink-0 w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                Dibuat</p>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">
                                {{ $created_at->format('d M Y') }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-2">
                        <div
                            class="flex-shrink-0 w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                            <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                Diperbarui</p>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">
                                {{ $updated_at->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>