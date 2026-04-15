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
use Filament\Navigation\NavigationGroup;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Support\HtmlString;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login() 
            
            /* --- MATIKAN DARK MODE --- */
            ->darkMode(false) 

            /* --- BRAND LOGO KUSTOM (TUT WURI) --- */
            ->brandLogo(fn () => view('filament.admin.logo'))
            ->brandLogoHeight('3.5rem')
            
            ->colors([
                'primary' => Color::Blue,
                'gray' => Color::Slate,
                'success' => Color::Emerald,
                'info' => Color::Sky,
            ])
            ->font('Plus Jakarta Sans')
            ->renderHook(
                'panels::body.start',
                fn () => new HtmlString('
                    <div class="bg-top-design"></div>
                    <style>
                        /* Background Biru Dashboard */
                        .bg-top-design {
                            position: absolute; top: 0; left: 0; width: 100%; height: 350px;
                            background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
                            z-index: 0; clip-path: polygon(0 0, 100% 0, 100% 80%, 0% 100%);
                            pointer-events: none;
                        }

                        /* Sidebar Putih Solid */
                        .fi-sidebar, .fi-sidebar-nav, .fi-sidebar-header {
                            background-color: white !important;
                        }
                        .fi-sidebar { border-right: 1px solid #e5e7eb; z-index: 20 !important; }

                        /* Header & Topbar Setup */
                        .fi-main { position: relative; z-index: 10; }
                        .fi-topbar { background-color: transparent !important; border: none !important; box-shadow: none !important; }
                        
                        /* Teks Header Putih agar kontras dengan Background Biru */
                        .fi-header-heading, .fi-breadcrumbs-item-label, .fi-breadcrumbs-item-separator { 
                            color: white !important; 
                        }

                        /* Menyesuaikan jarak logo agar tidak terlalu mepet */
                        .fi-sidebar-header {
                            padding-top: 1.5rem !important;
                            padding-bottom: 1.5rem !important;
                        }
                    </style>
                '),
            )
            ->navigationGroups([
                NavigationGroup::make()->label('MASTER DATA'),
                NavigationGroup::make()->label('OPSI'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([\App\Filament\Pages\Dashboard::class])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                \App\Filament\Widgets\StatsOverview::class,
                \App\Filament\Widgets\IncomeChart::class,
                \App\Filament\Widgets\TopBorrowerWidget::class,
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
            ->authMiddleware([Authenticate::class]);
    }
}