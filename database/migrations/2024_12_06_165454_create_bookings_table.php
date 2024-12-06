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
    Schema::create(table: 'bookings', callback: function (Blueprint $table): void {
      $table->id();
      $table->string(column: 'name')->require();
      $table->string(column: 'last_name')->require();
      $table->string(column: 'email')->require();
      $table->string(column: 'phone_number')->require();
      $table->string(column: 'phone_number_second')->nullable();
      $table->enum(column: 'status', allowed: [0, 1, 2, 3])->default(value: 0);
      $table->date(column: 'check_in')->require();
      $table->date(column: 'check_out')->require();
      $table->foreignId(column: 'room_id')->constrained()->cascadeOnDelete();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists(table: 'bookings');
  }
};
