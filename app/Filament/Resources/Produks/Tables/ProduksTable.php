<?php

namespace App\Filament\Resources\Produks\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
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

class ProduksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Gambar')
                    ->disk('public')
                    ->size(80)
                    ->defaultImageUrl(url('/images/placeholder.png')),

                TextColumn::make('name')
                    ->label('Nama Produk')
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->wrap(),

                TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR')
                    ->weight(FontWeight::Bold)
                    ->color('success'),

                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->color('primary'),

                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color('info'),

                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->wrap()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')

            ->recordActions([
                ViewAction::make()->icon('heroicon-o-eye'),

                EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->visible(fn() => Auth::user()?->hasRole('admin')),
            ])

            /**
             * 🚨 BULK ACTIONS
             */
            ->selectable(fn() => Auth::user()?->hasRole('user'))
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

                        $produkList = $records->map(function ($record) {
                            return "• {$record->name} - Rp " .
                                number_format($record->price, 0, ',', '.');
                        })->join("\n");

                        $tanggal = Carbon::parse($data['tanggal_meeting'])
                            ->translatedFormat('d F Y');

                        $jam = $data['jam_meeting'];

                        $message = "Halo Admin Lumin Park 👋\n\n";
                        $message .= "Saya ingin mengatur jadwal meeting untuk produk berikut:\n\n";
                        $message .= "━━━━━━━━━━━━━━━━━━━━━━\n";
                        $message .= "📋 *DAFTAR PRODUK*\n";
                        $message .= "━━━━━━━━━━━━━━━━━━━━━━\n";
                        $message .= $produkList;
                        $message .= "\n\n";
                        $message .= "━━━━━━━━━━━━━━━━━━━━━━\n";
                        $message .= "📅 *JADWAL MEETING*\n";
                        $message .= "━━━━━━━━━━━━━━━━━━━━━━\n";
                        $message .= "📆 Tanggal: {$tanggal}\n";
                        $message .= "🕐 Jam: {$jam}\n\n";
                        $message .= "━━━━━━━━━━━━━━━━━━━━━━\n";
                        $message .= "👤 *INFORMASI CUSTOMER*\n";
                        $message .= "━━━━━━━━━━━━━━━━━━━━━━\n";
                        $message .= "Nama: {$user->name}\n";
                        $message .= "Email: {$user->email}\n\n";
                        $message .= "Mohon konfirmasi ketersediaan jadwal meeting.\n";
                        $message .= "Terima kasih 🙏";

                        $adminPhone = '6281234567890'; // GANTI NOMOR

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
