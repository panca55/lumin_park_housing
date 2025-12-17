<style scoped>
    .product-panel-wrapper {
        width: 100%;
    }

    .product-panel {
        position: relative;
        width: 100%;
        border-radius: 16px;
        overflow: hidden;
        background-color: #ffffff;
        border: 2px solid #e5e7eb;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        min-height: 400px;
    }

    .hero-section {
        position: relative;
        width: 100%;
        height: 240px;
        background: linear-gradient(to bottom right, #f3f4f6, #e5e7eb);
    }

    .hero-image {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .hero-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.2), transparent);
    }

    .placeholder-container {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 12px;
    }

    .placeholder-icon {
        width: 80px;
        height: 80px;
        color: #9ca3af;
    }

    .placeholder-text {
        color: #6b7280;
        font-weight: 500;
    }

    .status-badge-wrapper {
        position: absolute;
        top: 16px;
        right: 16px;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 9999px;
        font-size: 14px;
        font-weight: 700;
        color: white;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .badge-available {
        background: linear-gradient(to right, #10b981, #059669);
    }

    .badge-unavailable {
        background: linear-gradient(to right, #ef4444, #dc2626);
    }

    .status-dot {
        width: 8px;
        height: 8px;
        background-color: white;
        border-radius: 9999px;
    }

    .pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.5;
        }
    }

    .content-area {
        padding: 24px;
    }

    .content-area>*+* {
        margin-top: 20px;
    }

    .title-section {
        margin-bottom: 12px;
    }

    .product-title {
        font-size: 30px;
        font-weight: 900;
        color: #111827;
        line-height: 1.2;
        margin: 0 0 12px 0;
    }

    .tags-container {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .tag {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        border: 1px solid;
    }

    .tag-category {
        background: linear-gradient(to bottom right, #eff6ff, #dbeafe);
        border-color: #bfdbfe;
        color: #1d4ed8;
    }

    .tag-type {
        background: linear-gradient(to bottom right, #faf5ff, #f3e8ff);
        border-color: #d8b4fe;
        color: #7e22ce;
    }

    .price-card {
        position: relative;
        overflow: hidden;
        border-radius: 12px;
        background: linear-gradient(to bottom right, #2563eb, #4f46e5, #7c3aed);
        padding: 20px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .price-bg-1 {
        position: absolute;
        top: 0;
        right: 0;
        width: 128px;
        height: 128px;
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 9999px;
        filter: blur(64px);
    }

    .price-bg-2 {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 96px;
        height: 96px;
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 9999px;
        filter: blur(40px);
    }

    .price-content {
        position: relative;
    }

    .price-label {
        font-size: 12px;
        font-weight: 500;
        color: rgba(255, 255, 255, 0.8);
        margin-bottom: 4px;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    .price-value {
        font-size: 30px;
        font-weight: 900;
        color: white;
        letter-spacing: -0.025em;
    }

    .description-section>*+* {
        margin-top: 10px;
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .header-line {
        width: 4px;
        height: 24px;
        background: linear-gradient(to bottom, #2563eb, #4f46e5);
        border-radius: 9999px;
    }

    .section-title {
        font-size: 18px;
        font-weight: 700;
        color: #111827;
        margin: 0;
    }

    .description-box {
        border-radius: 12px;
        background-color: #f9fafb;
        border: 1px solid #e5e7eb;
        padding: 20px;
    }

    .description-content {
        font-size: 14px;
        line-height: 1.75;
        color: #374151;
    }

    .description-content p {
        margin: 0 0 12px 0;
    }

    .description-content strong {
        font-weight: 700;
        color: #111827;
    }

    .description-content a {
        color: #2563eb;
        text-decoration: underline;
    }

    .metadata-section {
        padding-top: 16px;
        margin-top: 4px;
        border-top: 2px solid #e5e7eb;
    }

    .metadata-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .metadata-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .metadata-icon {
        flex-shrink: 0;
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .icon-blue {
        background-color: #dbeafe;
    }

    .icon-purple {
        background-color: #f3e8ff;
    }

    .metadata-icon svg {
        width: 16px;
        height: 16px;
    }

    .icon-blue svg {
        color: #2563eb;
    }

    .icon-purple svg {
        color: #7c3aed;
    }

    .metadata-text {
        flex: 1;
        min-width: 0;
    }

    .metadata-label {
        font-size: 12px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 2px;
    }

    .metadata-value {
        font-size: 14px;
        font-weight: 700;
        color: #111827;
    }
</style>

<div class="product-panel-wrapper">
    <div class="product-panel">

        {{-- HERO IMAGE --}}
        <div class="hero-section">
            @if($image)
                <img src="{{ Storage::url($image) }}" alt="{{ $name }}" class="hero-image" loading="lazy"
                    onerror="this.src='{{ url('/images/placeholder.png') }}'">
                <div class="hero-overlay"></div>
            @else
                <div class="placeholder-container">
                    <svg class="placeholder-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                    <p class="placeholder-text">Gambar tidak tersedia</p>
                </div>
            @endif

            {{-- STATUS BADGE --}}
            <div class="status-badge-wrapper">
                @if($is_available)
                    <span class="badge badge-available">
                        <span class="status-dot pulse"></span>
                        Tersedia
                    </span>
                @else
                    <span class="badge badge-unavailable">
                        <span class="status-dot"></span>
                        Tidak Tersedia
                    </span>
                @endif
            </div>
        </div>

        {{-- CONTENT AREA --}}
        <div class="content-area">

            {{-- TITLE & CATEGORY TAGS --}}
            <div class="title-section">
                <h1 class="product-title">{{ $name }}</h1>

                <div class="tags-container">
                    <span class="tag tag-category">{{ ucfirst($category) }}</span>
                    <span class="tag tag-type">{{ ucfirst($type) }}</span>
                </div>
            </div>

            {{-- PRICE SECTION --}}
            <div class="price-card">
                <div class="price-bg-1"></div>
                <div class="price-bg-2"></div>

                <div class="price-content">
                    <p class="price-label">Harga Produk</p>
                    <p class="price-value">Rp {{ number_format($price, 0, ',', '.') }}</p>
                </div>
            </div>

            {{-- DESCRIPTION SECTION --}}
            <div class="description-section">
                <div class="section-header">
                    <div class="header-line"></div>
                    <h3 class="section-title">Deskripsi Produk</h3>
                </div>

                <div class="description-box">
                    <div class="description-content">
                        {!! \Illuminate\Support\Str::markdown($description ?? '_Tidak ada deskripsi tersedia untuk produk ini._') !!}
                    </div>
                </div>
            </div>

            {{-- METADATA TIMESTAMPS --}}
            <div class="metadata-section">
                <div class="metadata-grid">
                    <div class="metadata-item">
                        <div class="metadata-icon icon-blue">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4"></path>
                            </svg>
                        </div>
                        <div class="metadata-text">
                            <p class="metadata-label">Dibuat</p>
                            <p class="metadata-value">{{ $created_at->format('d M Y') }}</p>
                        </div>
                    </div>

                    <div class="metadata-item">
                        <div class="metadata-icon icon-purple">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </path>
                            </svg>
                        </div>
                        <div class="metadata-text">
                            <p class="metadata-label">Diperbarui</p>
                            <p class="metadata-value">{{ $updated_at->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>