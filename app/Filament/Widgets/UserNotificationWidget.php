<?php

namespace App\Filament\Widgets;

use App\Models\MeetingRequest;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class UserNotificationWidget extends Widget
{
    protected string $view = 'filament.widgets.user-notification-widget';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 1;
    protected static bool $isLazy = false;
    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->hasRole('user');
    }

    public function getNotifications()
    {
        if (!Auth::check()) {
            return collect([]);
        }

        return MeetingRequest::getSoldProductNotifications(Auth::id())
            ->take(5); // Ambil 5 notifikasi terbaru
    }

    public function markAsRead($meetingRequestId)
    {
        $meetingRequest = MeetingRequest::where('id', $meetingRequestId)
            ->where('user_id', Auth::id())
            ->first();

        if ($meetingRequest) {
            $meetingRequest->markNotificationAsRead();

            // Refresh the widget
            $this->dispatch('$refresh');
        }
    }

    public function markAllAsRead()
    {
        MeetingRequest::where('user_id', Auth::id())
            ->where('notified_if_sold', true)
            ->whereNull('notification_read_at')
            ->update(['notification_read_at' => now()]);

        // Refresh the widget
        $this->dispatch('$refresh');
    }

    protected function getViewData(): array
    {
        return [
            'notifications' => $this->getNotifications(),
        ];
    }
}
