<?php

namespace App\Providers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\ServiceProvider;
use Illuminate\Testing\TestResponse;
use Inertia\Testing\AssertableInertia;

class TestingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (! $this->app->runningUnitTests()) {
            return;
        }

        AssertableInertia::macro('hasResource', function (string $key, JsonResource $resource) {
            $this->has($key);
            expect($this->prop($key))->toEqual($resource->response()->getData(true));

            return $this;
        });

        AssertableInertia::macro('hasPaginatedResource', function (string $key, ResourceCollection $collection) {
            $expectedData = $collection->response()->getData(true);
            expect($this->prop($key))->toHaveKeys(['data', 'links', 'meta']);
            expect($this->prop($key)['data'])->toEqual($expectedData['data']);

            return $this;
        });

        TestResponse::macro('assertHasResource', function (string $key, JsonResource $resource) {
            return $this->assertInertia(function (AssertableInertia $inertia) use ($key, $resource) {
                $inertia->hasResource($key, $resource);
            });
        });

        TestResponse::macro('assertHasPaginatedResource', function (string $key, ResourceCollection $resource) {
            return $this->assertInertia(function (AssertableInertia $inertia) use ($key, $resource) {
                $inertia->hasPaginatedResource($key, $resource);
            });
        });

        TestResponse::macro('assertHasComponent', function (string $key) {
            return $this->assertInertia(function (AssertableInertia $inertia) use ($key) {
                $inertia->component($key, true);
            });
        });

        TestResponse::macro('assertHasProp', function (string $key, mixed $value) {
            return $this->assertInertia(function (AssertableInertia $inertia) use ($key, $value) {
                $inertia->where($key, $value);
            });
        });
    }
}
