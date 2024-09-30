<?php

use App\Models\User;
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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('status_id')->constrained();

            $table->foreignId('customer_id')->constrained(
                table: 'users'
            );
            $table->foreignId('executor_id')->nullable()->constrained(
                table: 'users'
            );

            $table->foreignId('category_id')->constrained();
            $table->string('title');
            $table->string('description');
            $table->foreignId('city_id')->nullable()->constrained();
            $table->decimal('lat', 9, 6)->nullable();
            $table->decimal('long', 9, 6)->nullable();
            $table->string('address')->nullable();
            $table->integer('budget')->nullable();
            $table->timestamp('deadline')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
