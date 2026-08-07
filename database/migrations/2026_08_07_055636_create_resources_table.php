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
      Schema::create('resources', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('type');
        $table->enum('status', ['available', 'maintenance', 'unavailable'])->default('available');
        $table->unsignedInteger('capacity')->default(1);
        $table->timestamps();
      });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
      Schema::dropIfExists('resources');
    }
  };
