<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class ParentPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('parent')
            ->path('portal')
            ->breadcrumbs(false)
            ->spa()
            ->brandName("Portal Wali Siswa - MTs Miftahul 'Ulum")
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
                            Portal Wali - MTs Miftahul \'Ulum
                        </span>
                    </div>
                ')
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => Blade::render('
                    <script src="{{ config(\'midtrans.is_production\') ? \'https://app.midtrans.com/snap/snap.js\' : \'https://app.sandbox.midtrans.com/snap/snap.js\' }}" data-client-key="{{ config(\'midtrans.client_key\') }}"></script>
                    <script>
                        if (!window.__midtransSnapListenerAdded) {
                            window.__midtransSnapListenerAdded = true;
                            window.addEventListener("open-midtrans-snap", function(event) {
                                const token = event.detail.snapToken || (event.detail && event.detail[0] ? event.detail[0].snapToken : null);
                                if (token && window.snap) {
                                    window.snap.pay(token, {
                                        onSuccess: function(result) {
                                            window.location.href = "/payment/success";
                                        },
                                        onPending: function(result) {
                                            window.location.href = "/payment/pending";
                                        },
                                        onError: function(result) {
                                            window.location.href = "/payment/failed";
                                        },
                                        onClose: function() {
                                            console.log("User closed Midtrans snap popup");
                                        }
                                    });
                                }
                            });
                        }
                    </script>
                ')
            )
            ->login()
            ->colors([
                'primary' => Color::Emerald,
            ])
            ->discoverResources(in: app_path('Filament/Parent/Resources'), for: 'App\\Filament\\Parent\\Resources')
            ->discoverPages(in: app_path('Filament/Parent/Pages'), for: 'App\\Filament\\Parent\\Pages')
            ->pages([
                \App\Filament\Parent\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Parent/Widgets'), for: 'App\\Filament\\Parent\\Widgets')
            ->widgets([
                \App\Filament\Parent\Widgets\ParentOverviewWidget::class,
                \App\Filament\Parent\Widgets\UnpaidBillsWidget::class,
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
