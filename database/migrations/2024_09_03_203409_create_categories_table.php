<?php

use App\Models\Category;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public array $data = [
        ['name' => 'Курьерские услуги', 'icon' => 'courier'],
        ['name' => 'Уборка', 'icon' => 'clean'],
        ['name' => 'Переезды', 'icon' => 'cargo'],
        ['name' => 'Компьютерная помощь', 'icon' => 'neo'],
        ['name' => 'Ремонт квартирный', 'icon' => 'flat'],
        ['name' => 'Ремонт техники', 'icon' => 'repair'],
        ['name' => 'Красота', 'icon' => 'beauty'],
        ['name' => 'Фото', 'icon' => 'photo'],
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            $table->string('name')->unique();
            $table->string('icon')->nullable();

            $table->timestamps();
        });

        foreach ($this->data as $category) {
            Category::query()->create($category);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
