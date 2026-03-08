<?php

namespace App\Filament\Resources\AppSettingResource\Pages;

use App\Filament\Resources\AppSettingResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\AppSetting;

class CreateAppSetting extends CreateRecord
{
    protected static string $resource = AppSettingResource::class;

    protected ?string $heading = 'Tambah Pengaturan Baru';

    protected function afterCreate(): void
    {
        // Clear cache setelah create
        AppSetting::clearCache();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
