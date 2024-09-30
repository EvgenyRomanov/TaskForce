<?php

use App\Models\City;
use App\Models\Role;
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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained();
            $table->foreignId('city_id')->constrained();
            $table->string('mobile')->nullable();
            $table->string('telegram')->nullable();
            $table->string('about')->nullable();
            $table->dateTime('birth_date')->nullable();
            $table->string('avatar')->nullable();
            $table->integer('cnt_failed_tasks')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeignIdFor(Role::class);
            $table->dropForeignIdFor(City::class);
            $table->dropColumn('role_id');
            $table->dropColumn('city_id');
            $table->dropColumn('mobile');
            $table->dropColumn('telegram');
            $table->dropColumn('about');
            $table->dropColumn('birth_date');
            $table->dropColumn('avatar');
            $table->dropColumn('cnt_failed_tasks');
        });
    }
};
