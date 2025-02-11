<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('prices', function (Blueprint $table) {
      $table->id();
      $table->float('amount')->require();
      $table->string('stripe_id')->require();
      $table->boolean('is_default')->default(false);
      $table->foreignId('room_id')->require()->constrained()->onDelete('cascade');
      $table->foreignId('season_id')->nullable()->constrained()->onDelete('cascade');
      $table->softDeletes();
      $table->timestamps();

      $table->unique(['room_id', 'is_default'], 'unique_default_price');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('prices');
  }
};
