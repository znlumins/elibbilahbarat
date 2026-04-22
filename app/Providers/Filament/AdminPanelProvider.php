<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Auth;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login() 
            ->homeUrl('/') 
            ->authGuard('web')
            ->darkMode(false) 
            ->brandLogo(fn () => view('filament.admin.logo'))
            ->brandLogoHeight('3rem')
            ->userMenuItems([]) 
            ->colors([
                'primary' => '#3c8dbc',
                'gray' => Color::Slate,
            ])
            /* 1. CSS: HAPUS LOGO DI LOGIN & STYLE DASHBOARD */
            ->renderHook(
                'panels::body.start',
                fn () => new HtmlString('
                    <style>
                        /* MENGHAPUS LOGO KHUSUS DI HALAMAN LOGIN ADMIN */
                        .fi-logo { 
                            display: none !important; 
                        }

                        /* TAPI LOGO DI SIDEBAR TETAP MUNCUL */
                        .fi-sidebar-header .fi-logo { 
                            display: block !important; 
                        }

                        * { border-radius: 0px !important; }
                        body { background-color: #ecf0f5 !important; }

                        /* STYLE TOPBAR & SIDEBAR (AdminLTE) */
                        .fi-topbar, .fi-topbar nav, .fi-topbar-content { background-color: #3c8dbc !important; }
                        .fi-topbar * { color: white !important; }
                        .fi-user-menu { display: none !important; }
                        .fi-sidebar-header { background-color: #367fa9 !important; height: 4rem !important; }
                        .fi-sidebar { background-color: #222d32 !important; border: none !important; }

                        /* MENU SIDEBAR */
                        .fi-sidebar-nav .fi-sidebar-item:not(.fi-sidebar-item-active) .fi-sidebar-item-button * { color: #b8c7ce !important; }
                        .fi-sidebar-nav .fi-sidebar-item:not(.fi-sidebar-item-active) * { background-color: #1e282c !important; }
                        .fi-sidebar-nav .fi-sidebar-item-active, .fi-sidebar-nav .fi-sidebar-item-active button { background-color: #ffffff !important; border-left: 4px solid #3c8dbc !important; }
                        .fi-sidebar-nav .fi-sidebar-item-active * { color: #000000 !important; font-weight: bold !important; }

                        /* STATS BOXES */
                        .fi-stats-overview-stat { border: none !important; color: white !important; }
                        .fi-stats-overview-stat:nth-child(1) { background-color: #00c0ef !important; }
                        .fi-stats-overview-stat:nth-child(2) { background-color: #00a65a !important; }
                        .fi-stats-overview-stat:nth-child(3) { background-color: #f39c12 !important; }
                        .fi-stats-overview-stat:nth-child(4) { background-color: #dd4b39 !important; }
                        .fi-stats-overview-stat * { color: white !important; }
                        .fi-section { border-top: 3px solid #d2d6de !important; }
                    </style>
                '),
            )
            /* 2. PROTEKSI: TENDANG USER JIKA BUKAN ADMIN */
            ->renderHook(
                'panels::page.start',
                function() {
                    if (Auth::check() && Auth::user()->role !== 'admin') {
                        Auth::logout();
                        return redirect('/')->send();
                    }
                }
            )
            /* 3. TOMBOL KELUAR */
            ->renderHook(
                'panels::topbar.end',
                fn () => new HtmlString('
                    <form method="POST" action="/logout" id="logout-form-admin" style="display: none;">
                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                    </form>
                    <button type="button" 
                            onclick="if(confirm(\'Yakin ingin keluar?\')) document.getElementById(\'logout-form-admin\').submit();"
                            class="flex items-center gap-2 px-3 py-1 bg-[#367fa9] hover:bg-[#2c6484] text-white font-bold text-[10px] border border-[#2c6484] mr-4 shadow-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                        </svg>
                        KELUAR
                    </button>
                '),
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([\App\Filament\Pages\Dashboard::class])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([\App\Filament\Widgets\StatsOverview::class])
            ->middleware([
                \Illuminate\Cookie\Middleware\EncryptCookies::class,
                \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
                \Illuminate\Session\Middleware\StartSession::class,
                \Illuminate\Session\Middleware\AuthenticateSession::class,
                \Illuminate\View\Middleware\ShareErrorsFromSession::class,
                \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
                \Illuminate\Routing\Middleware\SubstituteBindings::class,
                \Filament\Http\Middleware\DisableBladeIconComponents::class,
                \Filament\Http\Middleware\DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}