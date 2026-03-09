<style>
    .notification-widget {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
        padding: 24px;
        margin-bottom: 20px;
    }

    .widget-header {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        gap: 12px;
    }

    .widget-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }

    .widget-title {
        font-size: 20px;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
    }

    .widget-subtitle {
        font-size: 14px;
        color: #6b7280;
        margin: 4px 0 0 0;
    }

    .notifications-container {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .notification-item {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 16px;
        transition: all 0.2s ease;
    }

    .notification-item:hover {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transform: translateY(-1px);
    }

    .notification-header {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 12px;
    }

    .status-dot {
        width: 8px;
        height: 8px;
        background: #ef4444;
        border-radius: 50%;
    }

    .notification-title {
        font-weight: 600;
        color: #1f2937;
        font-size: 16px;
        margin: 0;
    }

    .status-badge {
        background: #fef2f2;
        color: #dc2626;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }

    .notification-content {
        color: #4b5563;
        margin-bottom: 12px;
        line-height: 1.5;
    }

    .product-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-top: 12px;
    }

    .product-item {
        background: white;
        padding: 12px 16px;
        border-radius: 6px;
        border: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .product-name {
        font-weight: 600;
        color: #1f2937;
        flex: 1;
    }

    .product-category {
        color: #6b7280;
        font-size: 14px;
    }

    .product-price {
        font-weight: 600;
        color: #dc2626;
        font-size: 14px;
    }

    .notification-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 16px;
        padding-top: 12px;
        border-top: 1px solid #e5e7eb;
    }

    .meeting-info {
        display: flex;
        gap: 20px;
        font-size: 12px;
        color: #6b7280;
    }

    .read-button {
        background: #3b82f6;
        color: white;
        padding: 6px 16px;
        border: none;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .read-button:hover {
        background: #2563eb;
    }

    .timestamp {
        font-size: 12px;
        color: #9ca3af;
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        border: 2px dashed #d1d5db;
        border-radius: 8px;
        background: #f9fafb;
    }

    .empty-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 16px;
        color: #9ca3af;
    }

    .empty-title {
        font-size: 18px;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 8px;
    }

    .empty-description {
        color: #6b7280;
        margin-bottom: 0;
    }

    .footer-actions {
        margin-top: 20px;
        padding-top: 16px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .mark-all-button {
        background: transparent;
        color: #6b7280;
        border: 1px solid #d1d5db;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .mark-all-button:hover {
        background: #f3f4f6;
        color: #374151;
    }

    .view-all-link {
        color: #3b82f6;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: color 0.2s ease;
    }

    .view-all-link:hover {
        color: #2563eb;
    }
</style>

<div class="notification-widget">
    <div class="widget-header">
        <div class="widget-icon">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5-5-5h5zM15 17V3">
                </path>
            </svg>
        </div>
        <div>
            <h2 class="widget-title">Notifikasi Produk</h2>
            <p class="widget-subtitle">Status produk yang Anda minati</p>
        </div>
    </div>

    <div class="notifications-container">
        @forelse($notifications as $notification)
            <div class="notification-item">
                <div class="notification-header">
                    <div class="status-dot"></div>
                    <h3 class="notification-title">Produk Tidak Tersedia</h3>
                    <span class="status-badge">Terjual</span>
                </div>

                <div class="notification-content">
                    Produk yang Anda request untuk meeting sudah tidak tersedia:
                </div>

                <div class="product-list">
                    @foreach($notification->getSoldProducts() as $product)
                        <div class="product-item">
                            <div class="product-name">{{ $product->name }}</div>
                            <div class="product-category">{{ $product->category }}</div>
                            <div class="product-price">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                        </div>
                    @endforeach
                </div>

                <div class="notification-footer">
                    <div class="meeting-info">
                        <span>Meeting: {{ $notification->tanggal_meeting->format('d M Y') }}
                            {{ $notification->jam_meeting ? $notification->jam_meeting->format('H:i') : '' }}</span>
                        <span>Status: {{ ucfirst($notification->status) }}</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <span class="timestamp">{{ $notification->updated_at->diffForHumans() }}</span>
                        <button wire:click="markAsRead({{ $notification->id }})" class="read-button">
                            Tandai Dibaca
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="empty-title">Tidak ada notifikasi</h3>
                <p class="empty-description">Semua produk yang Anda minati masih tersedia.</p>
            </div>
        @endforelse
    </div>

    @if($notifications->count() > 0)
        <div class="footer-actions">
            <button wire:click="markAllAsRead" class="mark-all-button">
                Tandai Semua Dibaca
            </button>
            <a href="#" class="view-all-link">
                Lihat Semua Notifikasi →
            </a>
        </div>
    @endif
</div>