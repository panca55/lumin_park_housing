<?php

namespace App\Filament\Resources\AppSettingResource\Pages;

use App\Filament\Resources\AppSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Models\AppSetting;

class ListAppSettings extends ListRecords
{
    protected static string $resource = AppSettingResource::class;

    protected ?string $heading = 'Pengaturan Aplikasi';

    protected ?string $subheading = 'Kelola nomor WhatsApp admin dan pengaturan aplikasi lainnya';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Pengaturan'),

            Actions\Action::make('clear_all_cache')
                ->label('Clear All Cache')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->action(function () {
                    AppSetting::clearCache();
                    $this->notify('success', 'Cache berhasil dihapus');
                }),

            Actions\Action::make('quick_edit_whatsapp')
                ->label('Edit Cepat WhatsApp')
                ->icon('heroicon-o-phone')
                ->color('success')
                ->form([
                    \Filament\Forms\Components\TextInput::make('whatsapp_number')
                        ->label('Nomor WhatsApp Admin')
                        ->tel()
                        ->placeholder('628123456789')
                        ->helperText('Format: 628xxxxxxxxxx (tanpa tanda +)')
                        ->default(fn() => AppSetting::getAdminWhatsApp())
                        ->required()
                ])
                ->action(function (array $data) {
                    AppSetting::set('admin_whatsapp_number', $data['whatsapp_number']);
                    $this->notify('success', 'Nomor WhatsApp admin berhasil diubah');
                })
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Bisa tambah widget untuk preview settings jika diperlukan
        ];
    }
}
