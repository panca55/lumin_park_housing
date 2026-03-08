<?php

namespace App\Filament\Resources\AppSettingResource\Pages;

use App\Filament\Resources\AppSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\AppSetting;

class EditAppSetting extends EditRecord
{
    protected static string $resource = AppSettingResource::class;

    protected ?string $heading = 'Edit Pengaturan';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(
                    fn(?AppSetting $record) =>
                    !in_array($record?->key, [
                        'admin_whatsapp_number',
                        'company_name',
                        'whatsapp_message_template',
                        'max_products_per_page'
                    ])
                ),
        ];
    }

    protected function afterSave(): void
    {
        // Clear cache setelah update
        AppSetting::clearCache();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
