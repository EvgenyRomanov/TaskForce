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
        Schema::create('responses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('executor_id')->constrained(
                table: 'users'
            );

            $table->foreignId('task_id')->constrained();
            $table->text('comment')->nullable();
            $table->boolean('active')->default(true);
            $table->integer('budget')->nullable();
            $table->unique(['executor_id', 'task_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('responses');
    }
};
