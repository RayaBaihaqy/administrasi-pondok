<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->breadcrumbs(false)
            ->spa()
            // ->viteTheme('resources/css/filament/admin.css')
            ->brandName("Yayasan Perguruan Islam Miftahul 'Ulum")
            ->brandLogo(asset('images/logo.png'))
            ->darkModeBrandLogo(asset('images/logo.png'))
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('images/favicon.png'))
            ->renderHook(
                PanelsRenderHook::HEAD_START,
                fn (): string => Blade::render('
                    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset(\'images/favicon.png\') }}?v=3">
                    <link rel="shortcut icon" href="{{ asset(\'favicon.ico\') }}?v=3">
                ')
            )
            ->renderHook(
                PanelsRenderHook::TOPBAR_LOGO_AFTER,
                fn (): string => Blade::render('
                    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@700;800&display=swap">
                    <div class="flex items-center ml-5" style="margin-left: 1.25rem;">
                        <span style="font-family: \'Plus Jakarta Sans\', sans-serif; font-size: 1.05rem;" class="font-extrabold tracking-tight text-gray-900 dark:text-white">
                            YAPIM MTs Miftahul \'Ulum
                        </span>
                    </div>
                ')
            )
            ->login(\App\Filament\Pages\Auth\Login::class)
            ->profile(\App\Filament\Pages\Auth\EditProfile::class)
            ->userMenu(position: \Filament\Enums\UserMenuPosition::Sidebar)
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => Blade::render('
                    <style>
                        /* Custom Fixed Emerald Container for Sidebar Profile */
                        .fi-sidebar-footer {
                            margin: 0.75rem 0.75rem 1rem 0.75rem !important;
                            padding: 0.375rem !important;
                            background: linear-gradient(135deg, #064e3b 0%, #065f46 50%, #047857 100%) !important;
                            border: 1px solid rgba(52, 211, 153, 0.3) !important;
                            border-radius: 1rem !important;
                            box-shadow: 0 10px 25px -5px rgba(6, 78, 59, 0.4), 0 8px 10px -6px rgba(6, 78, 59, 0.3) !important;
                            flex-shrink: 0 !important;
                            position: sticky !important;
                            bottom: 0.5rem !important;
                            z-index: 20 !important;
                            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
                        }
                        .fi-sidebar-footer:hover {
                            box-shadow: 0 14px 28px -4px rgba(6, 78, 59, 0.5), 0 10px 12px -4px rgba(6, 78, 59, 0.35) !important;
                            border-color: rgba(52, 211, 153, 0.5) !important;
                        }
                        .fi-sidebar .fi-user-menu-trigger {
                            padding: 0.5rem 0.625rem !important;
                            border-radius: 0.75rem !important;
                            color: #ffffff !important;
                            transition: background-color 0.2s ease !important;
                        }
                        .fi-sidebar .fi-user-menu-trigger:hover,
                        .fi-sidebar .fi-user-menu-trigger:focus-visible {
                            background-color: rgba(255, 255, 255, 0.15) !important;
                        }
                        .fi-sidebar .fi-user-menu-trigger .fi-user-menu-trigger-text {
                            color: #ffffff !important;
                            font-weight: 700 !important;
                            font-size: 0.875rem !important;
                            letter-spacing: -0.01em !important;
                            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2) !important;
                        }
                        .fi-sidebar .fi-user-menu-trigger .fi-icon,
                        .fi-sidebar .fi-user-menu-trigger svg {
                            color: #a7f3d0 !important;
                        }
                        .fi-sidebar .fi-user-menu-trigger .fi-user-avatar,
                        .fi-sidebar .fi-user-menu-trigger .fi-avatar {
                            box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.4), 0 2px 4px rgba(0, 0, 0, 0.2) !important;
                        }
                    </style>
                ')
            )
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);

    }
}
