<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;

class EditProfile extends BaseEditProfile
{
    protected static ?string $title = 'Profile';

    public static function getLabel(): string
    {
        return 'Profile';
    }

    protected function getRedirectUrl(): ?string
    {
        return filament()->getUrl();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getNameFormComponent(),
                $this->getSignatureFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
                $this->getCurrentPasswordFormComponent(),
            ]);
    }

    protected function getNameFormComponent(): Component
    {
        return TextInput::make('name')
            ->label('Nama Bendahara')
            ->helperText('Nama lengkap bendahara beserta gelar yang akan dicantumkan pada seluruh dokumen kuitansi & invoice.')
            ->required()
            ->maxLength(255)
            ->autofocus();
    }

    protected function getSignatureFormComponent(): Component
    {
        return FileUpload::make('signature_path')
            ->label('Upload Tanda Tangan (TTD)')
            ->helperText('Unggah foto/scan tanda tangan (format PNG/JPG, disarankan berlatar transparan). Tanda tangan ini akan otomatis terpasang pada dokumen Kuitansi & Invoice PDF.')
            ->image()
            ->disk('public')
            ->directory('signatures')
            ->visibility('public')
            ->imageEditor()
            ->maxSize(2048);
    }
}
