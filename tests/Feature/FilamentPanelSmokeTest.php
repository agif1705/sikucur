<?php

namespace Tests\Feature;

use App\Models\User;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;
use Throwable;

class FilamentPanelSmokeTest extends TestCase
{
    public function test_filament_admin_pages_do_not_throw_server_errors(): void
    {
        try {
            $user = User::query()->find(1);
        } catch (Throwable $exception) {
            $this->markTestSkipped('Database is not available for Filament smoke testing: '.$exception->getMessage());
        }

        if (! $user) {
            $this->markTestSkipped('User ID 1 is required for Filament smoke testing.');
        }

        Filament::setCurrentPanel('admin');

        $routes = collect(Route::getRoutes())
            ->filter(fn ($route) => in_array('GET', $route->methods(), true))
            ->map(fn ($route) => $route->uri())
            ->filter(fn (string $uri) => str_starts_with($uri, 'admin'))
            ->reject(fn (string $uri) => str_contains($uri, '{'))
            ->reject(fn (string $uri) => $uri === 'admin/login')
            ->unique()
            ->sort()
            ->values();

        $this->assertNotEmpty($routes);

        foreach ($routes as $uri) {
            $response = $this->actingAs($user)->get('/'.$uri);

            $this->assertFalse(
                $response->isServerError(),
                "Filament route [{$uri}] returned {$response->getStatusCode()}."
            );
        }

        foreach ($this->editableResourceUrls() as $url) {
            $response = $this->actingAs($user)->get($url);

            $this->assertFalse(
                $response->isServerError(),
                "Filament edit URL [{$url}] returned {$response->getStatusCode()}."
            );
        }
    }

    private function editableResourceUrls(): array
    {
        return collect(glob(app_path('Filament/Resources/*Resource.php')))
            ->map(fn (string $path): string => 'App\\Filament\\Resources\\'.basename($path, '.php'))
            ->filter(fn (string $class): bool => is_subclass_of($class, Resource::class))
            ->map(function (string $class): ?string {
                if (! array_key_exists('edit', $class::getPages())) {
                    return null;
                }

                $model = $class::getModel();

                if (! $model || ! class_exists($model)) {
                    return null;
                }

                $record = $model::query()->first();

                if (! $record) {
                    return null;
                }

                return $class::getUrl('edit', ['record' => $record]);
            })
            ->filter()
            ->values()
            ->all();
    }
}
