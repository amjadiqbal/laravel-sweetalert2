<?php

namespace AmjadIqbal\SweetAlert2;

use AmjadIqbal\SweetAlert2\View\Components\SweetAlert2 as SweetAlert2Component;
use Composer\InstalledVersions;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class SweetAlert2ServiceProvider extends ServiceProvider
{
    public const SUPPORT_URL = 'https://amjad.com.pk/contact?utm_source=artisan&utm_medium=about&utm_campaign=laravel-sweetalert2';

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/sweetalert2.php', 'sweetalert2');

        $this->app->scoped(SweetAlert2::class);
        $this->app->alias(SweetAlert2::class, 'sweetalert2');
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'sweetalert2');

        Blade::component('sweetalert2', SweetAlert2Component::class);
        Blade::directive('sweetalert2', fn () => '<?php echo app(\AmjadIqbal\SweetAlert2\SweetAlert2::class)->render(); ?>');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/sweetalert2.php' => config_path('sweetalert2.php'),
            ], 'sweetalert2-config');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/sweetalert2'),
            ], 'sweetalert2-views');
        }

        AboutCommand::add('SweetAlert2', fn () => [
            'Version' => InstalledVersions::isInstalled('amjadiqbal/laravel-sweetalert2')
                ? (string) InstalledVersions::getPrettyVersion('amjadiqbal/laravel-sweetalert2')
                : 'unknown',
            'Support' => self::SUPPORT_URL,
        ]);
    }
}
