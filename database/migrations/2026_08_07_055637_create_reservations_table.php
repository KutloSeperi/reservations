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
      Schema::create('reservations', function (Blueprint $table) {
        $table->id();
        $table->foreignId('resource_id')->constrained()->cascadeOnDelete();
        $table->string('customer_name');
        $table->string('customer_email');
        $table->dateTime('start_at');
        $table->dateTime('end_at');
        $table->text('notes')->nullable();
        $table->enum('status', ['active', 'cancelled'])->default('active');
        $table->timestamps();

        $table->index(['resource_id', 'start_at', 'end_at']);
      });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
      Schema::dropIfExists('reservations');
    }
  };
