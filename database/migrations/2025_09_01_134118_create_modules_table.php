<?php

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
 Schema::create('modules', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->foreignUuid('course_id')->constrained('courses')->cascadeOnDelete();
    $table->string('title');
    $table->unsignedInteger('ordering')->default(1);
    $table->timestamps();
});
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
