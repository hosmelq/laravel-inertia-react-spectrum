<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('organization_user', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('organization_id')->index();
            $table->foreignId('user_id')->index();

            $table->timestamps();

            $table->unique(['organization_id', 'user_id']);
        });
    }
};
