<?php

declare(strict_types=1);

use function Pest\Laravel\get;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;
use Tests\Support\Models\TestModel;

it('set public id when creating', function (): void {
    $model = TestModel::query()->create();

    expect($model)
        ->public_id->toBeString()
        ->public_id->toHaveLength(26);
});

it('use public id to resolve route binding', function (): void {
    $model = TestModel::query()->create();

    Route::middleware(SubstituteBindings::class)
        ->get('/api/test/{model}', function (TestModel $model): void {});

    get('/api/test/'.$model->id)
        ->assertNotFound();

    get('/api/test/'.$model->public_id)
        ->assertOk();
});

it('throws 404 when model is not found by public id', function (): void {
    TestModel::findOrFailByPublicId('asdf');
})->throws(ModelNotFoundException::class);

it('finds a model by public id', function (): void {
    $model = TestModel::query()->create();

    expect($model->is(TestModel::findByPublicId($model->public_id)))->toBeTrue();
});
