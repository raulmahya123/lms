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
    Schema::create('plan_courses', function (Blueprint $table) {
        $table->id();
        $table->foreignId('plan_id')->constrained()->cascadeOnDelete();
        $table->foreignId('course_id')->constrained()->cascadeOnDelete();
        $table->unique(['plan_id','course_id']);
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_courses');
    }
};
