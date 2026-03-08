<?php

namespace App\Filament\Resources\Produks\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\MeetingRequest;
use App\Models\AppSetting;

class ProduksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                // Eager load relationships untuk mencegah N+1 queries
                $query->with(['gambarProduks', 'panoramaProduks', 'denahProduks']);

                // Apply role-based filtering
                $user = Auth::user();
                if ($user && $user->hasRole('user')) {
                    return $query->where('is_available', true);
                }
                return $query;
            })
            // Grid 3x3 = 9 produk per halaman
            ->defaultPaginationPageOption(9)
            ->paginationPageOptions([9, 18, 27, 36])
            ->deferLoading()
            ->columns([
                // Layout khusus untuk grid view 3x3
                Split::make([
                    ImageColumn::make('image')
                        ->label('Gambar')
                        ->disk('public')
                        ->size(120) // Ukuran lebih besar untuk grid
                        ->square()
                        ->defaultImageUrl(url('/images/placeholder.png')),

                    Stack::make([
                        TextColumn::make('name')
                            ->label('Nama Produk')
                            ->weight(FontWeight::Bold)
                            ->searchable()
                            ->wrap()
                            ->size('lg'),

                        TextColumn::make('price')
                            ->label('Harga')
                            ->money('IDR')
                            ->weight(FontWeight::Bold)
                            ->color('success')
                            ->size('md'),

                        Split::make([
                            TextColumn::make('category')
                                ->label('Kategori')
                                ->badge()
                                ->color('primary')
                                ->size('sm'),
                            TextColumn::make('type')
                                ->label('Tipe')
                                ->badge()
                                ->color('info')
                                ->size('sm'),

                            TextColumn::make('booking_stats')
                                ->label('Booking')
                                ->badge()
                                ->color('warning')
                                ->size('sm')
                                ->formatStateUsing(function ($record): string {
                                    $count = $record->getBookingCount();
                                    $pending = $record->getPendingBookingCount();

                                    if ($count === 0) {
                                        return 'Belum ada booking';
                                    }

                                    $text = "{$count}x dibooking";
                                    if ($pending > 0) {
                                        $text .= " ({$pending} pending)";
                                    }

                                    return $text;
                                })
                                ->tooltip(function ($record): string {
                                    $analytics = $record->getBookingAnalytics();
                                    return "Total: {$analytics['total_bookings']}\n" .
                                        "Pending: {$analytics['pending_bookings']}\n" .
                                        "Selesai: {$analytics['completed_bookings']}\n" .
                                        "Skor Popularitas: {$analytics['popularity_score']}";
                                }),
                        ])->from('md'),

                        TextColumn::make('description')
                            ->label('Deskripsi')
                            ->limit(50)
                            ->wrap()
                            ->color('gray')
                            ->size('sm')
                            ->toggleable(isToggledHiddenByDefault: true),

                        TextColumn::make('is_available')
                            ->label('Tersedia')
                            ->badge()
                            ->color(fn($state): string => match ((string)$state) {
                                '1' => 'success',
                                '0' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn($state): string => match ((string)$state) {
                                '1' => 'Ya',
                                '0' => 'Tidak',
                                default => 'Tidak Diketahui'
                            })
                            ->visible(fn() => Auth::user()?->hasRole('admin')),
                    ])->space(2)
                ])->from('md'),
            ])
            ->defaultSort('created_at', 'desc')

            ->recordActions([
                ViewAction::make()->icon('heroicon-o-eye'),
                DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->visible(fn() => Auth::user()?->hasRole('admin')),
                EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->visible(fn() => Auth::user()?->hasRole('admin')),
            ])

            /**
             * 🚨 BULK ACTIONS
             */
            ->selectable()
            ->bulkActions([
                /**
                 * 🟢 WHATSAPP MEETING (USER & ADMIN - SELALU VISIBLE)
                 */
                Action::make('hubungi_whatsapp')
                    ->label('Atur Meeting via WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->requiresConfirmation(false)
                    ->accessSelectedRecords()

                    ->form([
                        DatePicker::make('tanggal_meeting')
                            ->label('Tanggal Meeting')
                            ->required()
                            ->minDate(now())
                            ->displayFormat('d F Y'),

                        TimePicker::make('jam_meeting')
                            ->label('Jam Meeting')
                            ->required()
                            ->seconds(false),
                    ])

                    ->action(function (array $data, $records) {

                        $user = Auth::user();

                        // Simpan meeting request ke database untuk tracking
                        $produkIds = $records->pluck('id')->toArray();

                        $produkList = $records->map(function ($record) {
                            return "• {$record->name} - Rp " .
                                number_format($record->price, 0, ',', '.');
                        })->join("\n");

                        $tanggal = Carbon::parse($data['tanggal_meeting'])
                            ->translatedFormat('d F Y');

                        $jam = $data['jam_meeting'];

                        // Generate message using template from settings
                        $messageTemplate = AppSetting::get(
                            'whatsapp_message_template',
                            "Halo Admin {company_name} 👋\n\nSaya ingin mengatur jadwal meeting untuk produk berikut:\n\n{product_list}\n\n📅 Jadwal Meeting: {meeting_date} pada {meeting_time}\n\n👤 Informasi Customer:\nNama: {customer_name}\nEmail: {customer_email}\n\nMohon konfirmasi ketersediaan jadwal meeting.\nTerima kasih 🙏"
                        );

                        $message = str_replace([
                            '{company_name}',
                            '{product_list}',
                            '{meeting_date}',
                            '{meeting_time}',
                            '{customer_name}',
                            '{customer_email}'
                        ], [
                            AppSetting::getAppName(),
                            $produkList,
                            $tanggal,
                            $jam,
                            $user->name,
                            $user->email
                        ], $messageTemplate);

                        // Simpan meeting request untuk tracking dan notifikasi nanti
                        $meetingRequest = MeetingRequest::create([
                            'user_id' => $user->id,
                            'produk_ids' => $produkIds,
                            'tanggal_meeting' => $data['tanggal_meeting'],
                            'jam_meeting' => $data['jam_meeting'],
                            'status' => 'pending',
                            'whatsapp_message' => $message
                        ]);

                        // Auto-refresh booking count cache untuk produk yang terlibat
                        foreach ($records as $record) {
                            $record->refreshBookingCountCache();
                        }

                        // Get admin WhatsApp number from settings
                        $adminPhone = AppSetting::getAdminWhatsApp();

                        return redirect()->away(
                            'https://wa.me/' .
                                $adminPhone .
                                '?text=' .
                                urlencode($message)
                        );
                    }),

                /**
                 * 🔴 DELETE (ADMIN)
                 */
                DeleteBulkAction::make()
                    ->visible(fn() => Auth::user()?->hasRole('admin')),
            ]);
    }
}
