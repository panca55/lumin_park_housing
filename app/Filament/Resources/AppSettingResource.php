<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppSettingResource\Pages;
use App\Filament\Resources\AppSettingResource\Schemas\AppSettingForm;
use App\Models\AppSetting;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Schemas\Schema;

class AppSettingResource extends Resource
{
    protected static ?string $model = AppSetting::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
    public static function canViewAny(): bool
    {
        return Auth::check() && Auth::user()->hasRole('admin');
    }

    public static function form(Schema $schema): Schema
    {
        return AppSettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Setting')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('key')
                    ->label('Key')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('value')
                    ->label('Nilai Saat Ini')
                    ->limit(50)
                    ->tooltip(fn(?AppSetting $record) => $record?->value)
                    ->formatStateUsing(function (?AppSetting $record) {
                        if (!$record) return '';

                        return match ($record->type) {
                            'boolean' => $record->value ? '✅ Ya' : '❌ Tidak',
                            'tel' => $record->value ? '📱 ' . $record->value : '-',
                            'email' => $record->value ? '✉️ ' . $record->value : '-',
                            default => $record->value ?: '-'
                        };
                    }),

                Tables\Columns\IconColumn::make('is_public')
                    ->label('Publik')
                    ->boolean()
                    ->tooltip(fn($state) => $state ? 'Dapat diakses user biasa' : 'Hanya admin'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Diubah')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipe')
                    ->options([
                        'text' => 'Text',
                        'textarea' => 'Textarea',
                        'number' => 'Number',
                        'email' => 'Email',
                        'tel' => 'Telephone',
                        'boolean' => 'Boolean',
                        'select' => 'Select'
                    ]),

                Tables\Filters\TernaryFilter::make('is_public')
                    ->label('Publik')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->visible(
                        fn(?AppSetting $record) =>
                        !in_array($record?->key, [
                            'admin_whatsapp_number',
                            'company_name',
                            'whatsapp_message_template',
                            'max_products_per_page'
                        ])
                    ), // Tidak bisa hapus setting penting
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('clear_cache')
                        ->label('Clear Cache')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->action(function () {
                            AppSetting::clearCache();
                        })
                        ->deselectRecordsAfterCompletion()
                ]),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAppSettings::route('/'),
            'create' => Pages\CreateAppSetting::route('/create'),
            'edit' => Pages\EditAppSetting::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
