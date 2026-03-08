<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Produk;
use Illuminate\Database\Eloquent\Builder;

class MostBookedProductsTable extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    public function getHeading(): string
    {
        return 'Produk Paling Sering Dibooking';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Gambar')
                    ->disk('public')
                    ->size(60)
                    ->square(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Produk')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('booking_count')
                    ->label('Total Booking')
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(fn($record) => $record->getBookingCount() . 'x')
                    ->sortable(),

                Tables\Columns\TextColumn::make('pending_bookings')
                    ->label('Pending')
                    ->badge()
                    ->color('warning')
                    ->formatStateUsing(fn($record) => $record->getPendingBookingCount() . 'x'),

                Tables\Columns\TextColumn::make('popularity_score')
                    ->label('Skor Popularitas')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(function ($record) {
                        $analytics = $record->getBookingAnalytics();
                        return $analytics['popularity_score'];
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('is_available')
                    ->label('Status')
                    ->badge()
                    ->color(fn($state): string => match ((string)$state) {
                        '1' => 'success',
                        '0' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state): string => match ((string)$state) {
                        '1' => 'Tersedia',
                        '0' => 'Tidak Tersedia',
                        default => 'Tidak Diketahui'
                    }),
            ])
            ->defaultSort('booking_count', 'desc')
            ->actions([
                Tables\Actions\Action::make('view_details')
                    ->label('Detail Analytics')
                    ->icon('heroicon-o-chart-bar')
                    ->color('info')
                    ->modalContent(function ($record) {
                        $analytics = $record->getBookingAnalytics();
                        $trend = $analytics['recent_trend'];

                        return view('filament.widgets.booking-analytics-modal', [
                            'record' => $record,
                            'analytics' => $analytics,
                            'trend' => $trend,
                        ]);
                    })
                    ->modalHeading(fn($record) => 'Analytics untuk ' . $record->name)
                    ->modalWidth('3xl'),
            ])
            ->paginated([10, 25, 50]);
    }

    protected function getTableQuery(): Builder
    {
        // Get products that have at least 1 booking
        return Produk::query()
            ->whereHas('meetingRequests', function ($query) {
                $query->where('status', '!=', 'cancelled');
            })
            ->with(['gambarProduks'])
            ->orderByDesc(function ($query) {
                // Custom ordering by booking count would be complex in SQL
                // We'll handle this in the table sort instead
                return $query->select('id');
            });
    }
}
