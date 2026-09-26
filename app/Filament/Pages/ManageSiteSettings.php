<?php

namespace App\Filament\Pages;

use App\Models\SiteSettings;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;
use BackedEnum;

class ManageSiteSettings extends Page
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected string $view = 'filament.pages.manage-site-settings';
    public ?array $data = [];

    public function mount(): void
    {
        $settings = SiteSettings::firstOrCreate([]);
        $this->form->fill($settings->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('address'),
                TextInput::make('phone')->tel(),
                TextInput::make('whatsapp')->tel(),
                TextInput::make('email')->email(),
                TextInput::make('instagram_url')->url(),
                TextInput::make('facebook_url')->url(),
                TextInput::make('tiktok_url')->url(),
                TextInput::make('twitter_url')->url(),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Settings')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $settings = SiteSettings::firstOrCreate([]);
        $settings->update($this->form->getState());

        Notification::make()
            ->success()
            ->title('Settings updated')
            ->send();
    }
}
