<?php

use App\Models\Status;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Status::query()->create(['name' => Status::NEW]);
        Status::query()->create(['name' => Status::CANCELED]);
        Status::query()->create(['name' => Status::IN_PROGRESS]);
        Status::query()->create(['name' => Status::DONE]);
        Status::query()->create(['name' => Status::FAILED]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statuses');
    }
};
