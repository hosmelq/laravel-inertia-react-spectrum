<?php

declare(strict_types=1);

namespace Tests\Support\Models;

use App\Models\Concerns\HasPublicId;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class TestModel extends Model implements HasMedia
{
    use HasPublicId;
    use InteractsWithMedia;

    public $timestamps = false;

    protected $table = 'test_models';
}
