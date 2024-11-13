<?php

namespace App\Providers;

use Google\Client;
use Google\Service\Drive;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use Masbug\Flysystem\GoogleDriveAdapter;

class AppServiceProvider extends ServiceProvider
{
    private function loadGoogleStorageDriver(string $driverName = 'google') {
        try {
            Storage::extend($driverName, function($app, $config) {
                $options = [];

                if (!empty($config['teamDriveId'] ?? null)) {
                    $options['teamDriveId'] = $config['teamDriveId'];
                }

                $client = new Client();
                $client->setClientId($config['clientId']);
                $client->setClientSecret($config['clientSecret']);
                $client->refreshToken($config['refreshToken']);

                $service = new Drive($client);
                $adapter = new GoogleDriveAdapter($service, $config['folder'] ?? '/', $options);
                $driver = new Filesystem($adapter);

                return new FilesystemAdapter($driver, $adapter);
            });
        } catch(\Exception $e) {
            dd($e->getMessage());
            // your exception handling logic
        }
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->isLocal()) {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadGoogleStorageDriver();
        //
    }
}
