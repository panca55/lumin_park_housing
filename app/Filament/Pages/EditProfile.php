<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\TextInput;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Filament\Notifications\Notification;

class EditProfile extends Page implements HasForms
{
    use InteractsWithForms;
    protected static string $panel = 'dashboard';
    protected static ?string $navigationLabel = 'Profil Saya';
    // hide di navigasi
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $title = 'Edit Profil';
    protected string $view = 'filament.pages.edit-profile';
    public ?array $data = [];
    protected function getForms(): array
    {
        return ['form'];
    }

    public function mount(): void
    {
        /** @var User $user */ $user = Auth::user();
        $this->form->fill(['name' => $user->name, 'email' => $user->email]);
    }
    public function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('name')->label('Nama Lengkap')->required()->columnSpanFull(), TextInput::make('email')->email()->required()->columnSpanFull(), TextInput::make('password')->password()->revealable()->helperText('Kosongkan jika tidak ingin mengubah password'), TextInput::make('password_confirmation')->password()->same('password'),])->statePath('data')->columns(2);
    }
    protected function getFormActions(): array
    {
        return [Action::make('save')->label('Simpan Perubahan')->submit('save')->color('primary'),];
    }
    public function save(): void
    {
        /** @var User $user */
        $user = Auth::user();
        $data = $this->form->getState();

        $passwordChanged = false;

        $user->name = $data['name'];
        $user->email = $data['email'];

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
            $passwordChanged = true;
        }

        $user->save();

        // 🔑 WAJIB: login ulang HANYA jika password berubah
        if ($passwordChanged) {
            Auth::login($user);
        }

        Notification::make()
            ->title('Profil berhasil diperbarui')
            ->success()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return route('filament.admin.pages.dashboard');
    }
}
