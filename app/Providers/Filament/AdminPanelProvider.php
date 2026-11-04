<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use Filament\Enums\ThemeMode;
use Filament\FontProviders\BunnyFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName(new HtmlString(
                '<span class="tmcc-brand">'
                .'<img src="'.e(asset('image/logo_tova_matano.png')).'" alt="Logo Tova Matano Care Center" class="tmcc-brand-logo">'
                .'<span class="tmcc-brand-copy">'
                .'<span class="tmcc-brand-name">Tova Matano</span>'
                .'<span class="tmcc-brand-tagline">Care Center &middot; Admin</span>'
                .'</span>'
                .'</span>'
            ))
            ->favicon(asset('favicon.ico'))
            ->font('Work Sans', provider: BunnyFontProvider::class)
            ->serifFont('Fraunces', provider: BunnyFontProvider::class)
            ->colors([
                'primary' => [
                    50 => '#eff9fa',
                    100 => '#d7eff2',
                    200 => '#afe0e5',
                    300 => '#7cc8d1',
                    400 => '#47aab7',
                    500 => '#176873',
                    600 => '#1e7f8c',
                    700 => '#176873',
                    800 => '#145a64',
                    900 => '#124a53',
                    950 => '#0a2c32',
                ],
                'info' => [
                    50 => '#eef3f6',
                    100 => '#d4e2e9',
                    200 => '#a9c5d2',
                    300 => '#7aa4b8',
                    400 => '#4a819b',
                    500 => '#265f78',
                    600 => '#123a52',
                    700 => '#0f3146',
                    800 => '#0d2c40',
                    900 => '#0b2536',
                    950 => '#061620',
                ],
                'success' => [
                    50 => '#eef8f3',
                    100 => '#d5eee2',
                    200 => '#abdec7',
                    300 => '#78c5a5',
                    400 => '#46a37f',
                    500 => '#276a4e',
                    600 => '#2f7d5c',
                    700 => '#276a4e',
                    800 => '#215a43',
                    900 => '#1c4b39',
                    950 => '#0e2b20',
                ],
                'warning' => [
                    50 => '#fdf8ec',
                    100 => '#faedd0',
                    200 => '#f4d99b',
                    300 => '#edc163',
                    400 => '#e8b14d',
                    500 => '#c9861f',
                    600 => '#e3a23a',
                    700 => '#a96f16',
                    800 => '#8a5a10',
                    900 => '#714a0e',
                    950 => '#402806',
                ],
                'danger' => [
                    50 => '#fcf1f0',
                    100 => '#f8dddb',
                    200 => '#f0bab7',
                    300 => '#e58e89',
                    400 => '#d4635d',
                    500 => '#983832',
                    600 => '#b4453f',
                    700 => '#983832',
                    800 => '#7d2d28',
                    900 => '#672622',
                    950 => '#391310',
                ],
                'gray' => Color::Stone,
            ])
            ->darkMode(false)
            ->defaultThemeMode(ThemeMode::Light)
            ->themeSwitcher(false)
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth(Width::SevenExtraLarge)
            ->navigationGroups([
                NavigationGroup::make('Konten Website')
                    ->icon(Heroicon::OutlinedNewspaper),
                NavigationGroup::make('Lingkungan & Monitoring')
                    ->icon(Heroicon::OutlinedBeaker),
                NavigationGroup::make('Keanggotaan & Donasi')
                    ->icon(Heroicon::OutlinedUserGroup),
                NavigationGroup::make('Pengaturan')
                    ->icon(Heroicon::OutlinedCog6Tooth)
                    ->collapsible(false),
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): HtmlString => new HtmlString(
                    '<meta name="color-scheme" content="light only">'
                    .'<link rel="stylesheet" href="'.e(asset('css/tmcc-admin.css')).'?v='.e((string) @filemtime(public_path('css/tmcc-admin.css'))).'">'
                    ."\n"
                    .'<script>'
                    ."\n"
                    .'document.documentElement.classList.remove("dark");'
                    ."\n"
                    .'document.documentElement.setAttribute("data-bs-theme","light");'
                    ."\n"
                    .'localStorage.setItem("theme","light");'
                    ."\n"
                    .'</script>'
                ),
            )
            ->bootUsing(function (): void {
                app()->setLocale('id');
            })
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
