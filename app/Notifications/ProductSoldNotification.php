<?php

namespace App\Notifications;

use App\Models\Produk;
use App\Models\MeetingRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProductSoldNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $produk;
    protected $meetingRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct(Produk $produk, MeetingRequest $meetingRequest)
    {
        $this->produk = $produk;
        $this->meetingRequest = $meetingRequest;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        // Bisa ditambah 'database', 'sms' dll sesuai kebutuhan 
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $tanggalMeeting = $this->meetingRequest->tanggal_meeting->format('d F Y');
        $jamMeeting = $this->meetingRequest->jam_meeting->format('H:i');

        return (new MailMessage)
            ->subject('Pemberitahuan: Produk yang Pernah Anda Minati Sudah Terjual')
            ->greeting("Halo {$notifiable->name}!")
            ->line("Kami ingin memberitahukan bahwa produk yang pernah Anda ajukan meeting telah terjual:")
            ->line("")
            ->line("**{$this->produk->name}**")
            ->line("Harga: Rp " . number_format($this->produk->price, 0, ',', '.'))
            ->line("Kategori: {$this->produk->category}")
            ->line("")
            ->line("**Detail Meeting Anda:**")
            ->line("📅 Tanggal yang diajukan: {$tanggalMeeting}")
            ->line("🕐 Jam yang diajukan: {$jamMeeting}")
            ->line("")
            ->line("Meskipun produk ini sudah tidak tersedia, kami memiliki produk serupa yang mungkin menarik bagi Anda.")
            ->action('Lihat Produk Lainnya', url('/'))
            ->line("")
            ->line("Terima kasih atas minat Anda pada Lumin Park Housing!")
            ->line("Tim Lumin Park Housing")
            ->salutation("Salam hangat,\nCustomer Service Lumin Park");
    }

    /**
     * Get the array representation of the notification (untuk database channel).
     */
    public function toArray($notifiable): array
    {
        return [
            'produk_id' => $this->produk->id,
            'produk_name' => $this->produk->name,
            'produk_price' => $this->produk->price,
            'meeting_date' => $this->meetingRequest->tanggal_meeting,
            'meeting_time' => $this->meetingRequest->jam_meeting,
            'message' => "Produk {$this->produk->name} yang pernah Anda minati sudah terjual"
        ];
    }
}
